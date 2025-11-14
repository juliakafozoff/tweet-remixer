<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class TweetGenerator
{
    protected string $model;

    protected ?string $apiKey;

    protected string $endpointTemplate;

    /**
     * @var array<int, string>
     */
    protected array $fallbackModels = [
        'gemini-1.5-flash',
        'gemini-1.5-flash-latest',
        'gemini-1.5-pro-latest',
        'gemini-pro',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash-001');
        $this->endpointTemplate = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';
    }

    /**
     * @return array<int, string>
     *
     * @throws RuntimeException
     */
    public function generateFromPost(Post $post, int $count = 10): array
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('Gemini API key is missing. Set GEMINI_API_KEY in your environment configuration.');
        }

        $prompt = $this->buildPrompt($post, $count);

        if (empty($this->model)) {
            throw new RuntimeException('Gemini model is not configured. Set GEMINI_MODEL.');
        }

        $availableModels = $this->listAvailableModels();
        $modelsToTry = array_values(array_unique(array_merge([$this->model], $this->fallbackModels, $availableModels)));

        $errors = [];

        foreach ($modelsToTry as $model) {
            try {
                $text = $this->requestTextFromModel($model, $prompt);
                $tweets = $this->prepareTweets($text, $count);

                if (count($tweets) === 0) {
                    throw new RuntimeException('Gemini did not produce any valid tweets.');
                }

                if ($model !== $this->model) {
                    logger()->info('Gemini fallback model used for tweet generation.', ['model' => $model]);
                }

                return $tweets;
            } catch (RuntimeException $exception) {
                $errors[$model] = $exception->getMessage();

                if (! $this->isModelUnavailable($exception->getMessage())) {
                    throw $exception;
                }
            }
        }

        $lastMessage = end($errors) ?: 'Unknown Gemini error.';
        throw new RuntimeException('Gemini could not generate tweets after trying models: ' . implode(', ', array_keys($errors)) . '. Last error: ' . $lastMessage);
    }

    protected function extractText(array $payload): string
    {
        $candidates = $payload['candidates'] ?? [];

        $combined = '';

        foreach ($candidates as $candidate) {
            $parts = $candidate['content']['parts'] ?? [];

            foreach ($parts as $part) {
                if (! empty($part['text'])) {
                    $combined .= $part['text'] . PHP_EOL;
                }
            }
        }

        return trim($combined);
    }

    protected function buildPrompt(Post $post, int $count): string
    {
        $bodyPreview = Str::limit($post->body, 2000);

        return <<<PROMPT
You are an expert social media manager. Read the following blog post and craft {$count} compelling tweets.

Requirements:
- Each tweet must be unique and stand on its own.
- Tweets must be at most 280 characters.
- Do not use hashtags (#) or mentions (@) in any tweet.
- Keep a helpful, informative, and energetic tone suited for tech-savvy readers.
- Include clear calls to action or key takeaways when relevant.
- Avoid emojis unless they add clarity.
- Return the tweets as a simple numbered list, one tweet per line.

Blog post title: {$post->title}
Blog post content:
{$bodyPreview}
PROMPT;
    }

    protected function cleanLine(string $line): string
    {
        $line = trim($line);
        $line = preg_replace('/^\d+\.\s*/', '', $line) ?? $line;
        $line = preg_replace('/^[\-\*\x{2022}]\s*/u', '', $line) ?? $line;

        return trim($line, " \t\n\r\0\x0B\"“”‘’");
    }

    /**
     * @throws RuntimeException
     */
    protected function requestTextFromModel(string $model, string $prompt): string
    {
        $endpoint = sprintf($this->endpointTemplate, $model);

        $response = Http::acceptJson()
            ->timeout(30)
            ->post($endpoint . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 32,
                    'topP' => 0.95,
                ],
            ]);

        if (! $response->successful()) {
            $error = $response->json('error.message') ?? $response->body();
            throw new RuntimeException("Gemini could not generate tweets using {$model}: {$error}");
        }

        $text = $this->extractText($response->json());

        if ($text === '') {
            throw new RuntimeException("Gemini returned an empty response using {$model}.");
        }

        return $text;
    }

    protected function prepareTweets(string $text, int $count): array
    {
        $tweets = [];

        foreach (preg_split('/\R+/', $text) as $line) {
            $line = $this->cleanLine($line);

            if ($line === '') {
                continue;
            }

            if (mb_strlen($line) > 280) {
                $line = Str::limit($line, 280, '');
                $line = rtrim($line);
            }

            if (! str_contains($line, '#') && ! str_contains($line, '@')) {
                $tweets[] = $line;
            }

            if (count($tweets) === $count) {
                break;
            }
        }

        return $tweets;
    }

    protected function isModelUnavailable(string $message): bool
    {
        $message = Str::lower($message);

        return Str::contains($message, 'not found')
            || Str::contains($message, 'unsupported')
            || Str::contains($message, 'listmodels');
    }

    protected function listAvailableModels(): array
    {
        $response = Http::acceptJson()
            ->timeout(15)
            ->get('https://generativelanguage.googleapis.com/v1beta/models', [
                'key' => $this->apiKey,
            ]);

        if (! $response->successful()) {
            return [];
        }

        $models = $response->json('models', []);

        if (! is_array($models)) {
            return [];
        }

        return collect($models)
            ->filter(function ($model) {
                $methods = $model['supportedGenerationMethods'] ?? [];

                return in_array('generateContent', $methods, true);
            })
            ->pluck('name')
            ->map(fn ($name) => Str::replaceFirst('models/', '', (string) $name))
            ->filter()
            ->values()
            ->all();
    }
}


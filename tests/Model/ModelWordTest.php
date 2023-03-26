<?php

declare(strict_types=1);

namespace ClassroomTest\Model;

final class ModelWordTest extends ModelDatabase {
    protected string $_modelName = 'Word';

    protected function getTestData(): array {
        return [
            [
                'english' => 'english 1',
                'russian' => 'russian 1',
                'transcription' => 'transcription 1',
                'audio' => '1',
                'audioFileName' => 'audioFileName.mp3',
                'audioFileType' => 'mp3',
                'audioSource' => 'audioSource.mp3',
                'image' => '1',
                'imageFileName' => 'imageFileName.jpg',
                'imageFileType' => 'image/jpeg',
                'imageSource' => 'imageSource.jpg',
                'partOfSpeech' => 'noun',
                'isPlural' => '1',
                'isCountable' => '1',
                'isPhrase' => '1',
                'disabled' => '0',
                'deleted' => '0',
            ],
            [
                'english' => 'english 2',
                'russian' => 'russian 2',
                'transcription' => 'transcription 2',
                'audio' => '0',
                'audioFileName' => null,
                'audioFileType' => null,
                'audioSource' => null,
                'image' => '0',
                'imageFileName' => null,
                'imageFileType' => null,
                'imageSource' => null,
                'partOfSpeech' => 'verb',
                'isPlural' => '0',
                'isCountable' => '0',
                'isPhrase' => '0',
                'disabled' => '1',
                'deleted' => '1',
            ],
        ];
    }
}

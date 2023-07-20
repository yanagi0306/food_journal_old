<?php

namespace App\Services\Base\CsvWrappers;

use App\Exceptions\SkipImportException;
use App\Helpers\ConvertHelper;
use App\Helpers\ValidationHelper;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * 各カラムクラスの継承元クラス
 */
abstract class ColumnBase
{
    /**
     * 取り込まれる値
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * 取り込まれた値の名称
     * @var string
     */
    protected string $valueName = '';

    /**
     * 例外発生時のメッセージ（スキップ）
     * @var string
     */
    protected string $skipMessage = '';

    /**
     * 許可された型
     * 取り込み型に変更がある場合は継承先で変更する
     * integer:整数 string:文字列 boolean:真偽 double:浮動小数点数 date:日付型 timestamp:タイムスタンプ
     * @var string
     */
    protected string $permittedValueType = 'integer';

    /**
     * $this->valueの先頭末尾の空白削除を許可
     * @var bool
     */
    protected bool $isTrimSpaces = true;

    /**
     * 空からnullへの置換を許可
     * @var bool
     */
    protected bool $isReplaceEmptyWithNull = true;

    /**
     * 禁止文字を設定
     * ※必要があれば' 'を追加する
     * 削除する場合は$isRemoveForbiddenChars = trueに設定
     * @var array 配列で禁止文字を指定
     */
    protected array $forbiddenChars = [
        "\n",
        "\r",
        "\t",
        "\0",
        "\x1F",
        '@',
        '#',
        '%',
        '!',
        '^',
        '&',
        '"',
        "'",
    ];


    /**
     * @var bool 禁止文字の削除を許可
     */
    protected bool $isRemoveForbiddenChars = true;

    /**
     * 「:」区切りで左側の分離をする場合はtrueを設定
     * @var ?bool
     */
    protected ?bool $isExtractLeft = null;

    /**
     * 「:」区切りで右側の分離の許可
     * @var ?bool
     */
    protected ?bool $isExtractRight = null;

    /**
     * 許可された値がある場合継承先で定義する
     * 配列に定義されいる場合は値がチェックされる
     * 許可された値
     * デフォルトは未設定
     */
    protected ?array $permittedValues = null;

    /**
     * 無効な値がある場合継承先で定義する
     * 配列に定義されいる場合は値がチェックされる
     * デフォルトはnull(空はnullへ変換済み)
     */
    protected ?array $invalidValues = [null];

    /**
     * $this->valueの負の整数を許可
     * @var bool
     */
    protected bool $isAllowNegativeInteger = false;

    /**
     * @para string|null $value
     * @para string $valueName
     * @throws SkipImportException|Exception
     */
    public function __construct(?string $value, string $valueName)
    {
        $this->value     = $value;
        $this->valueName = $valueName;

        // 値の変換処理
        $this->convertValues();

        // 値の検証処理
        $this->validateValues();

        // バリデーションパートでエラー発生時はスルー
        if ($this->skipMessage) {
            throw new SkipImportException($this->skipMessage);
        }
    }

    /**
     * 値の取得
     * @return mixed value
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * 値の変換処理
     * @throws Exception
     */
    private function convertValues(): void
    {
        // 許可されている場合は先頭末尾の空白を削除
        if ($this->isTrimSpaces) {
            $this->value = ConvertHelper::trimSpaces($this->value);
        }

        // 許可されている場合は禁止文字を削除
        if ($this->isRemoveForbiddenChars) {
            $this->value = ConvertHelper::removeForbiddenChars($this->value, $this->valueName, $this->forbiddenChars);
        }

        // 許可されている場合「:」区切りで左側を分離
        if ($this->isExtractLeft) {
            $this->value = ConvertHelper::extractLeft($this->value, $this->valueName);
        }

        // 許可されている場合「:」区切りで右側を分離
        if ($this->isExtractRight) {
            $this->value = ConvertHelper::extractRight($this->value, $this->valueName);
        }

        // 許可されている場合は空をnullに置換
        if ($this->isReplaceEmptyWithNull && $this->value === '') {
            $this->value = ConvertHelper::replaceEmptyWithNull($this->value);
        }

        // 定義されている場合は型の変換
        if ($this->permittedValueType) {
            $this->value = ConvertHelper::convertToType($this->value, $this->valueName, $this->permittedValueType);
        }
    }

    /**
     * 値の検証処理
     * @throws Exception|SkipImportException
     */
    private function validateValues(): void
    {
        // 型のチェック
        if ($this->permittedValueType && ValidationHelper::validateValueType($this->value, $this->permittedValueType) === false) {
            $this->addSkipMessage("許可された型:{$this->permittedValueType} 取り込まれた型に誤りがあります。");
        }

        // 許可された値を検証
        if ($this->permittedValues && ValidationHelper::validatePermittedValue($this->value, $this->permittedValues) === false) {
            $this->addSkipMessage("許可された値:(" . implode(',', $this->permittedValues) . ") 許可された値以外が含まれています。");
        }

        // 許可されていない値か検証
        if ($this->invalidValues && ValidationHelper::validateInvalidValues($this->value, $this->invalidValues) === false) {
            $this->addSkipMessage("許可されていない値:(" . implode(',', $this->invalidValues) . ") 許可されていない値が含まれています。");
        }

        // 負の値か検証
        if (!$this->isAllowNegativeInteger && ValidationHelper::validatePositiveValue($this->value, $this->permittedValueType) === false) {
            $this->addSkipMessage("負の値が検出されました。");
        }
    }

    /**
     * スキップメッセージの追加
     * @param string $message
     * @throws SkipImportException
     */
    private function addSkipMessage(string $message): void
    {
        $value = $this->value ?? '未入力';
        throw new SkipImportException("項目名[{$this->valueName}] 値:({$value}) {$message}");
    }
}

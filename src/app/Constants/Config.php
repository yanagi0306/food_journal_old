<?php

namespace App\Constants;

/**
 * 共通定数クラス
 *  <<制約事項>>
 *  ・すべてapp_config.phpで呼び出して使用します。
 *  ・const以外を定義することは禁止します。
 *  ・単独で読み込むことは禁止します。
 *  ・このファイル内で別ファイルを読み込むことは禁止します。
 */
class Config
{
    /**
     * food_journal ディレクトリ階層
     */
    const BASE_DIR   = '/workspace';
    const LOGS_DIR   = '/workspace/storage/logs';
    const DATA_DIR   = '/data';
    const UPLOAD_DIR = '/data/upload';
}

<?php

namespace App\Services\Order\Usen;

use App\Constants\UsenConstants;
use App\Exceptions\SkipImportException;
use App\Services\Order\Usen\Wrappers\OrderProductMaster;
use Exception;
use Illuminate\Support\Facades\Log;

class CsvOrderProductMasterRow
{
    private int                $companyId;
    private OrderProductMaster $orderProductMaster;

    /**
     * @param mixed $row
     * @param int   $companyId
     * @throws SkipImportException
     */
    public function __construct(mixed $row, int $companyId)
    {
        $this->companyId = $companyId;

        if (count($row) !== UsenConstants::USEN_CSV_ROW_COUNT) {
            Log::info(print_r($row, true));
            throw new Exception('不正な列数を持つ連携ファイルが検出されました。正しい桁列数:' . UsenConstants::USEN_CSV_ROW_COUNT . ' 検出された列数:' . count($row));
        }

        $this->orderProductMaster = new OrderProductMaster([
                                                               'category1' => $row[68],
                                                               'category2' => $row[69],
                                                               'category3' => $row[70],
                                                               'category4' => $row[71],
                                                               'category5' => $row[72],
                                                               'product'   => $row[74],
                                                               'unitPrice' => $row[78],
                                                               'unitCost'  => $row[80],
                                                               'quantity'  => $row[83],
                                                           ]);
    }

    /**
     * OrderProductMasterテーブル登録用のデータを取得する
     * @return array
     */
    public function getOrderProductMasterForRegistration(): array
    {
        $orderProductMaster = $this->orderProductMaster->getValues();

        return [
            'company_id'          => $this->companyId,
            'product_cd'          => $orderProductMaster['productCd'],
            'product_name'        => $orderProductMaster['productName'],
            'unit_cost'           => $orderProductMaster['unitCost'],
            'original_unit_cost'  => $orderProductMaster['unitCost'],
            'unit_price'          => $orderProductMaster['unitPrice'],
            'original_unit_price' => $orderProductMaster['unitPrice'],
            'category1'           => $orderProductMaster['category1'],
            'category2'           => $orderProductMaster['category2'],
            'category3'           => $orderProductMaster['category3'],
            'category4'           => $orderProductMaster['category4'],
            'category5'           => $orderProductMaster['category5'],
        ];
    }

    /**
     * OrderProductMasterテーブル更新用のデータを取得する
     * 初期価格、初期理論原価は既存がnullの場合のみ更新データを返却
     * @param \App\Models\OrderProductMaster $existingProductMaster
     * @return array
     */
    public function getOrderProductMasterForUpdate(\App\Models\OrderProductMaster $existingProductMaster): array
    {
        $orderProductMaster = $this->orderProductMaster->getValues();

        $updateData = [
            'product_cd'   => $orderProductMaster['productCd'],
            'product_name' => $orderProductMaster['productName'],
            'unit_cost'    => $orderProductMaster['unitCost'],
            'unit_price'   => $orderProductMaster['unitPrice'],
            'category1'    => $orderProductMaster['category1'],
            'category2'    => $orderProductMaster['category2'],
            'category3'    => $orderProductMaster['category3'],
            'category4'    => $orderProductMaster['category4'],
            'category5'    => $orderProductMaster['category5'],
        ];

        if (null !== $existingProductMaster['original_unit_cost']) {
            $updateData['original_unit_cost'] = $existingProductMaster['original_unit_cost'];
        }

        if (null !== $existingProductMaster['original_unit_price']) {
            $updateData['original_unit_price'] = $existingProductMaster['original_unit_price'];
        }

        return $updateData;
    }

    /**
     * 商品コードを取得する
     * @return string
     */
    public function getProductCd(): string
    {
        $productMaster = $this->orderProductMaster->getValues();
        return $productMaster['productCd'];
    }


}

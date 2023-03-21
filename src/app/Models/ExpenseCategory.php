<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $table = 'expense_category';

    protected $fillable = [
        'company_id',
        'parent_expense_category_id',
        'cat_cd',
        'cat_name',
    ];

    /**
     * companyテーブル リレーション設定
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * parent_expense_categoryテーブル リレーション設定
     * @return BelongsTo
     */
    public function parentExpenseCategory(): BelongsTo
    {
        return $this->belongsTo(ParentExpenseCategory::class);
    }

    /**
     * expense_typeテーブル リレーション設定
     * @return BelongsTo
     */
    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    /**
     * expenseテーブル リレーション設定
     * @return HasMany
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * expense_budgetテーブル リレーション設定
     * @return HasMany
     */
    public function expenseBudgets(): HasMany
    {
        return $this->hasMany(ExpenseBudget::class);
    }

}

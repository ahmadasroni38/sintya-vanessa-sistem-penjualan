<?php

namespace App\Http\Policies;

use App\Models\User;
use App\Models\ChartOfAccount;
use Illuminate\Auth\Access\Response;

class ChartOfAccountPolicy
{
    /**
     * Determine whether the user can view any chart of accounts.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermission('chart-of-accounts.view')
            ? Response::allow()
            : Response::deny('You do not have permission to view chart of accounts.');
    }

    /**
     * Determine whether the user can view the chart of account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response
     */
    public function view(User $user, ChartOfAccount $chartOfAccount): Response
    {
        // Check basic permission
        if (!$user->hasPermission('chart-of-accounts.view')) {
            return Response::deny('You do not have permission to view chart of accounts.');
        }

        // Additional business logic checks can be added here
        // For example, check if user belongs to same company/department

        return Response::allow();
    }

    /**
     * Determine whether the user can create chart of accounts.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function create(User $user): Response
    {
        // Check basic permission
        if (!$user->hasPermission('chart-of-accounts.create')) {
            return Response::deny('You do not have permission to create chart of accounts.');
        }

        // Additional business logic validation
        // For example, check if user has reached account creation limit
        // or if user belongs to a department that allows account creation

        return Response::allow();
    }

    /**
     * Determine whether the user can update the chart of account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response
     */
    public function update(User $user, ChartOfAccount $chartOfAccount): Response
    {
        // Check basic permission
        if (!$user->hasPermission('chart-of-accounts.edit')) {
            return Response::deny('You do not have permission to edit chart of accounts.');
        }

        // Business logic: Check if account has journal entries
        if ($chartOfAccount->journalEntryDetails()->count() > 0) {
            // Only allow editing certain fields if account has transactions
            $allowedFields = ['description', 'is_active'];
            // This can be handled in the controller/form validation
        }

        // Business logic: Check hierarchy constraints
        if ($chartOfAccount->children()->count() > 0) {
            // May need additional validation when changing account type or parent
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the chart of account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, ChartOfAccount $chartOfAccount): Response
    {
        // Check basic permission
        if (!$user->hasPermission('chart-of-accounts.delete')) {
            return Response::deny('You do not have permission to delete chart of accounts.');
        }

        // Business logic: Check if account has child accounts
        if ($chartOfAccount->children()->count() > 0) {
            return Response::deny('Cannot delete account with child accounts. Please delete or reassign child accounts first.');
        }

        // Business logic: Check if account has journal entries
        if ($chartOfAccount->journalEntryDetails()->count() > 0) {
            return Response::deny('Cannot delete account with journal entries. This account has ' . $chartOfAccount->journalEntryDetails()->count() . ' transaction records.');
        }

        // Business logic: Check if account is a system account
        if ($chartOfAccount->level === 1 && in_array($chartOfAccount->account_type, ['asset', 'liability', 'equity', 'revenue', 'expense'])) {
            // Additional check for top-level system accounts
            $systemAccountCodes = ['1-1000', '2-2000', '3-3000', '4-4000', '5-5000'];
            if (in_array($chartOfAccount->account_code, $systemAccountCodes)) {
                return Response::deny('Cannot delete system-defined top-level accounts.');
            }
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the chart of account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response
     */
    public function restore(User $user, ChartOfAccount $chartOfAccount): Response
    {
        // Check basic permission (same as create)
        if (!$user->hasPermission('chart-of-accounts.create')) {
            return Response::deny('You do not have permission to restore chart of accounts.');
        }

        // Business logic: Check if parent account exists and is active
        if ($chartOfAccount->parent_id) {
            $parent = ChartOfAccount::find($chartOfAccount->parent_id);
            if (!$parent || !$parent->is_active) {
                return Response::deny('Cannot restore account because parent account is not available or inactive.');
            }
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can force delete the chart of account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response
     */
    public function forceDelete(User $user, ChartOfAccount $chartOfAccount): Response
    {
        // Only super admins or users with special permission can force delete
        if (!$user->hasPermission('chart-of-accounts.force-delete')) {
            return Response::deny('You do not have permission to permanently delete chart of accounts.');
        }

        // Additional checks for force delete can be added here
        // For example, require additional approval or logging

        return Response::allow();
    }

    /**
     * Determine whether the user can view the account balance.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Auth\Access\Response
     */
    public function viewBalance(User $user, ChartOfAccount $chartOfAccount): Response
    {
        // Check basic view permission
        if (!$user->hasPermission('chart-of-accounts.view')) {
            return Response::deny('You do not have permission to view account balances.');
        }

        // Additional business logic for sensitive financial data
        // For example, restrict balance view for certain account types or user roles
        if (in_array($chartOfAccount->account_type, ['equity', 'revenue']) && !$user->hasPermission('financial.reports.view')) {
            return Response::deny('You do not have permission to view sensitive financial information.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can export chart of accounts.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function export(User $user): Response
    {
        // Check basic view permission plus export permission
        if (!$user->hasPermission('chart-of-accounts.view') || !$user->hasPermission('chart-of-accounts.export')) {
            return Response::deny('You do not have permission to export chart of accounts.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can bulk update chart of accounts.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function bulkUpdate(User $user): Response
    {
        // Check basic edit permission plus bulk operation permission
        if (!$user->hasPermission('chart-of-accounts.edit') || !$user->hasPermission('chart-of-accounts.bulk-update')) {
            return Response::deny('You do not have permission to bulk update chart of accounts.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can bulk delete chart of accounts.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response
     */
    public function bulkDelete(User $user): Response
    {
        // Check basic delete permission plus bulk operation permission
        if (!$user->hasPermission('chart-of-accounts.delete') || !$user->hasPermission('chart-of-accounts.bulk-delete')) {
            return Response::deny('You do not have permission to bulk delete chart of accounts.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can change the parent of a chart of account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @param  int|null  $newParentId
     * @return \Illuminate\Auth\Access\Response
     */
    public function changeParent(User $user, ChartOfAccount $chartOfAccount, ?int $newParentId): Response
    {
        // Check basic edit permission
        if (!$user->hasPermission('chart-of-accounts.edit')) {
            return Response::deny('You do not have permission to modify chart of accounts.');
        }

        // Business logic: Check if new parent would create circular reference
        if ($newParentId && $this->wouldCreateCircularReference($chartOfAccount->id, $newParentId)) {
            return Response::deny('Cannot move account: would create circular reference in hierarchy.');
        }

        // Business logic: Check if new parent is compatible
        if ($newParentId) {
            $newParent = ChartOfAccount::find($newParentId);
            if (!$newParent) {
                return Response::deny('Selected parent account does not exist.');
            }

            if (!$this->isAccountTypeCompatible($newParent->account_type, $chartOfAccount->account_type)) {
                return Response::deny('Account type is not compatible with new parent account type.');
            }

            // Check if new level would exceed maximum
            $newLevel = $newParent->level + 1;
            if ($newLevel > 5) {
                return Response::deny('Cannot move account: would exceed maximum hierarchy level (5).');
            }
        }

        return Response::allow();
    }

    /**
     * Helper method to check if changing parent would create circular reference
     *
     * @param  int  $accountId
     * @param  int  $newParentId
     * @return bool
     */
    private function wouldCreateCircularReference(int $accountId, int $newParentId): bool
    {
        $currentParent = ChartOfAccount::find($newParentId);

        while ($currentParent) {
            if ($currentParent->id === $accountId) {
                return true;
            }
            $currentParent = $currentParent->parent;
        }

        return false;
    }

    /**
     * Helper method to check if account types are compatible
     *
     * @param  string  $parentType
     * @param  string  $childType
     * @return bool
     */
    private function isAccountTypeCompatible(string $parentType, string $childType): bool
    {
        $compatibilityMatrix = [
            'asset' => ['asset'],
            'liability' => ['liability'],
            'equity' => ['equity'],
            'revenue' => ['revenue'],
            'expense' => ['expense']
        ];

        return in_array($childType, $compatibilityMatrix[$parentType] ?? []);
    }
}

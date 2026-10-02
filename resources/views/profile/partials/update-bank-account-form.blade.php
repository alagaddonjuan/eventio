<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Payout Bank Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your bank account details for receiving ticket payouts.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="bank_name" :value="__('Bank Name')" />
            <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="old('bank_name', $user->bankAccount->bank_name ?? '')" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
        </div>

        <div>
            <x-input-label for="account_number" :value="__('Account Number')" />
            <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" :value="old('account_number', $user->bankAccount->account_number ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
        </div>

        <div>
            <x-input-label for="account_name" :value="__('Account Name')" />
            <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" :value="old('account_name', $user->bankAccount->account_name ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('account_name')" />
        </div>
        
        <div>
            <x-input-label for="bank_code" :value="__('Bank Sort Code / Swift Code (Optional)')" />
            <x-text-input id="bank_code" name="bank_code" type="text" class="mt-1 block w-full" :value="old('bank_code', $user->bankAccount->bank_code ?? '')" />
            <x-input-error class="mt-2" :messages="$errors->get('bank_code')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'bank-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

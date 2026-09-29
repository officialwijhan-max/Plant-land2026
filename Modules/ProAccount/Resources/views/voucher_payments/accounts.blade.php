@if (isset($update_account_list))
    <select class="primary_select mb-15 payment_from" name="credit_account_id" id="account_id" required>
        <option>{{trans('account.Select one')}}</option>
        @foreach ($update_account_list as $key => $account)
            <option value="{{ $account->id }}" @if ($selected_account_id == $account->id) selected @endif>{{ $account->name }}</option>
        @endforeach
    </select>
@else
    <select class="primary_select mb-15 payment_from" name="credit_account_id" id="account_id" required>
        <option>{{trans('account.Select one')}}</option>
        @foreach ($account_list as $key => $account)
            <option value="{{ $account->id }}">{{ $account->name }}</option>
        @endforeach
    </select>
@endif

<div class="inline-page-menu my-4">
    <ul class="list-unstyled">

        <li class="{{ Request::is('admin/business-settings/provider-terms-condition') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.provider-terms-condition') }}">{{ \App\CPU\translate('Provider_Terms_&_Conditions') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/seller-terms-condition') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.seller-terms-condition') }}">{{ \App\CPU\translate('Seller_Terms_&_Conditions') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/terms-condition') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.terms-condition') }}">{{ \App\CPU\translate('Terms_&_Conditions') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/privacy-policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.privacy-policy') }}">{{ \App\CPU\translate('Privacy_Policy') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/e-wallet-Policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.e-wallet-Policy') }}">{{ \App\CPU\translate('E_wallet_Policy') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/shipping-policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.shipping-policy') }}">{{ \App\CPU\translate('Shipping_Policy') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/secure-payment-policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.secure-payment-policy') }}">{{ \App\CPU\translate('Secure_Payment_Policy ') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/instant-delivery-policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.instant-delivery-policy') }}">{{ \App\CPU\translate('Instant_Delivery_Policy') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/page/refund-policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.page', ['refund-policy']) }}">{{ \App\CPU\translate('Return_&_refund_policy') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/page/return-policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.page', ['return-policy']) }}">{{ \App\CPU\translate('Return_Policy') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/page/cancellation-policy') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.page', ['cancellation-policy']) }}">{{ \App\CPU\translate('Cancellation_Policy') }}</a>
        </li>
        <li class="{{ Request::is('admin/business-settings/about-us') ? 'active' : '' }}"><a
                href="{{ route('admin.business-settings.about-us') }}">{{ \App\CPU\translate('About_Us') }}</a></li>
        <li class="{{ Request::is('admin/helpTopic/list') ? 'active' : '' }}"><a
                href="{{ route('admin.helpTopic.list') }}">{{ \App\CPU\translate('FAQ') }}</a></li>
        <li class="{{ Request::is('admin/providerHelpTopic/list') ? 'active' : '' }}"><a
                href="{{ route('admin.providerHelpTopic.list') }}">{{ \App\CPU\translate('Provider FAQ') }}</a></li>
        @if (theme_root_path() == 'theme_fashion')
            <li style="display:none;"
                class="{{ Request::is('admin/business-settings/features-section') ? 'active' : '' }}"><a
                    href="{{ route('admin.business-settings.features-section') }}">{{ translate('features_Section') }}</a>
            </li>
        @endif
    </ul>
</div>

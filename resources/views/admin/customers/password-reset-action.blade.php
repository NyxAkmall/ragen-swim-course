<section class="account-management-card" style="border-color: #bae6fd; background: #f0f9ff;">
    <div class="account-management-content">
        <div class="account-management-icon account-icon-success">
            <svg
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <rect
                    x="5"
                    y="10"
                    width="14"
                    height="11"
                    rx="2"
                    stroke="currentColor"
                    stroke-width="1.8"
                />

                <path
                    d="M8 10V7a4 4 0 0 1 8 0v3"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                />
            </svg>
        </div>

        <div class="account-management-text">
            <span class="account-management-label">
                Keamanan Akun
            </span>

            <h2>Reset Password Customer</h2>

            <p>
                Kirim tautan reset password ke email
                <strong>{{ $customer->email }}</strong>.
                Customer akan membuat password baru melalui tautan tersebut.
                Admin tidak dapat melihat atau menentukan password Customer.
            </p>
        </div>
    </div>

    <div class="account-management-action">
        <form
            method="POST"
            action="{{ route('admin.customers.password-reset.send', $customer) }}"
            onsubmit="return confirm('Kirim tautan reset password ke email Customer ini?');"
        >
            @csrf

            <button
                type="submit"
                class="account-action-button activate-button"
                style="cursor: pointer;"
            >
                Kirim Link Reset Password
            </button>
        </form>
    </div>
</section>
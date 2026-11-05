@if ($coach->user && $coach->user->role === 'coach')
    <div class="card side-card">

        <div class="side-card-heading">
            Keamanan Akun Coach
        </div>

        <div class="status-information status-information-success">
            <strong>Reset Password Coach</strong>

            <p>
                Kirim tautan reset password ke email
                {{ $coach->user->email }}.
                Coach dapat membuat password baru melalui tautan tersebut.
                Admin tidak dapat melihat atau menentukan password Coach.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('admin.coaches.password-reset.send', $coach) }}"
            onsubmit="return confirm('Kirim tautan reset password ke email Coach ini?');"
        >
            @csrf

            <button
                type="submit"
                class="status-action-button success-button"
                style="cursor: pointer;"
            >
                Kirim Link Reset Password
            </button>
        </form>

    </div>
@endif
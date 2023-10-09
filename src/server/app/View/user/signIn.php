<?php
function alert($title, $message)
{
    $type = 'error';
    require __DIR__ . '/../components/alert.php';
}

?>


<div class="signin-container row">
    <img src="/assets/images/Suzume.webp" alt="Sign In Image" class="signin-poster" />
    <div class="right-side">
        <div class="main-container">
            <div class="welcome-text">
                <h2 class="welcome-text__h2">Hello Again!</h2>
                <p class="welcome-text__h1">Welcome back! Please provide your details</p>
            </div>
            <?php if (isset($model['error'])): ?>
                <?php alert('Failed to Sign in', $model['error']); ?>
            <?php endif; ?>

            <form class="inputs" action="/signin" method="post">
                <div class="parameter">
                    <label for="email" class="input-required">Email</label>
                    <input type="email" name="email" id="email" class="input-default" placeholder="Enter email"
                        value="<?= $model['data']['email'] ?? "" ?>">
                </div>
                <div class="parameter">
                    <label for="password" class="input-required">Password</label>
                    <input type="password" name="password" id="password" class="input-default"
                        placeholder="Enter password">
                </div>
                <button class="btn-bold" type="submit">
                    Sign In
                </button>
                <p>Don't have an account? <a href="/signup" class="signup-link">Sign up</a></p>
            </form>
        </div>
    </div>
</div>
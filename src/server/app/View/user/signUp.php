<div class="signup-container row">
    <img src="/assets/images/Tomorrow.webp" alt="Sign Up Image" class="signup-poster"/>
    <div class="right-side">
        <div class="main-container">
            <div class="welcome-text">
                <h2 class="welcome-text__h2">Let’s Get Started!</h2>
                <p class="welcome-text__h1">Glad to see you joining us! Please provide your details</p>
            </div>
            
            <?php if (isset($model['error'])) { ?>
                <div class="alert-error alert-error-signup">
                    <p>
                        <?= $model['error'] ?>
                    </p>
                </div>
            <?php } ?>

            <form class="inputs">
                <div class="parameter">
                    <label for="email" class="input-required">Email</label>
                    <input type="email" name="email" id="email" class="input-default"
                           value="<?= $_POST["email"] ?? "" ?>">
                </div>
                <div class="parameter">
                    <label for="password" class="input-required">Password</label>
                    <input type="password" name="password" id="password" class="input-default"
                           value="<?= $_POST["password"] ?? "" ?>"/>
                </div>
                <div class="parameter">
                    <label for="passwordConfirm" class="input-required">Confirm Password</label>
                    <input type="password" name="passwordConfirm" id="passwordConfirm" class="input-default"
                           value="<?= $_POST["passwordConfirm"] ?? "" ?>"/>
                </div>
                <button class="btn-bold" type="submit">
                    Sign Up
                </button>
            </form>

        </div>
    </div>
</div>

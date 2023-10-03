<main>
    <div class="signup-container row">
        <img src="https://m.media-amazon.com/images/I/71Nau-0ZheL._AC_UF894,1000_QL80_.jpg" />
        <div class="right-side">
            <div class="main-container">
                <div class="welcome-text">
                    <h2>Let’s Get Started!</h2>
                    <h4>Glad to see you joining us! Please provide your details</h4>
                </div>
                <?php if (isset($model['error'])) { ?>
                    <div class="alert-error alert-error-signup">
                        <p>
                            <?= $model['error'] ?>
                        </p>
                    </div>
                <?php } ?>

                <div class="input-container">
                    <div class="inputs">
                        <div class="parameter">
                            <p>Email (required)</p>
                            <div class="container-1">
                                <div class="container-2">
                                    <input type="email" name="email" id="email" class="input-default" value="<?= $_POST["email"] ?? "" ?>">
                                    </input>
                                </div>
                            </div>
                        </div>
                        <div class="parameter">
                            <p>Password (required)</p>
                            <div class="container-1">
                                <div class="container-2">
                                    <input type="password" name="password" id="password" class="input-default" value="<?= $_POST["password"] ?? "" ?>">
                                    </input>
                                </div>
                            </div>
                        </div>
                        <div class="parameter">
                            <p>Confirm Password (required) </p>
                            <div class="container-1">
                                <div class="container-2">
                                    <input type="password2" name="password2" id="password2" class="input-default" value="<?= $_POST["password2"] ?? "" ?>">
                                    </input>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn">
                        <div class="btn-text">SIGN UP</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
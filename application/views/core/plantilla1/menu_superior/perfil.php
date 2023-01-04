<div class="indicator indicator--trigger--click">
    <a href="account-login.html" class="indicator__button">
        <span class="indicator__icon">
            <svg width="32" height="32">
                <path d="M16,18C9.4,18,4,23.4,4,30H2c0-6.2,4-11.5,9.6-13.3C9.4,15.3,8,12.8,8,10c0-4.4,3.6-8,8-8s8,3.6,8,8c0,2.8-1.5,5.3-3.6,6.7
C26,18.5,30,23.8,30,30h-2C28,23.4,22.6,18,16,18z M22,10c0-3.3-2.7-6-6-6s-6,2.7-6,6s2.7,6,6,6S22,13.3,22,10z" />
            </svg>
        </span>
        <span class="indicator__title">Hello, Log In</span>
        <span class="indicator__value">My Account</span>
    </a>
    <div class="indicator__content">
        <div class="account-menu">
            <form class="account-menu__form">
                <div class="account-menu__form-title">
                    Log In to Your Account
                </div>
                <div class="form-group">
                    <label for="header-signin-email" class="sr-only">Email address</label>
                    <input id="header-signin-email" type="email" class="form-control form-control-sm" placeholder="Email address">
                </div>
                <div class="form-group">
                    <label for="header-signin-password" class="sr-only">Password</label>
                    <div class="account-menu__form-forgot">
                        <input id="header-signin-password" type="password" class="form-control form-control-sm" placeholder="Password">
                        <a href="" class="account-menu__form-forgot-link">Forgot?</a>
                    </div>
                </div>
                <div class="form-group account-menu__form-button">
                    <button type="submit" class="btn btn-primary btn-sm">Login</button>
                </div>
                <div class="account-menu__form-link">
                    <a href="account-login.html">Create An Account</a>
                </div>
            </form>
            <div class="account-menu__divider"></div>
            <a href="" class="account-menu__user">
                <div class="account-menu__user-avatar">
                    <img src="<?php echo base_url(); ?>images/avatars/avatar-4.jpg" alt="">
                </div>
                <div class="account-menu__user-info">
                    <div class="account-menu__user-name">Ryan Ford</div>
                    <div class="account-menu__user-email">red-parts@example.com</div>
                </div>
            </a>
            <div class="account-menu__divider"></div>
            <ul class="account-menu__links">
                <li><a href="account-dashboard.html">Dashboard</a></li>
                <li><a href="account-dashboard.html">Garage</a></li>
                <li><a href="account-profile.html">Edit Profile</a></li>
                <li><a href="account-orders.html">Order History</a></li>
                <li><a href="account-addresses.html">Addresses</a></li>
            </ul>
            <div class="account-menu__divider"></div>
            <ul class="account-menu__links">
                <li><a href="account-login.html">Logout</a></li>
            </ul>
        </div>
    </div>
</div>
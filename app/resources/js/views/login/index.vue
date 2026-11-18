<template>
  <div class="login-page">

    <!-- =========================
         LEFT / BRANDING SECTION
    ========================== -->
    <div class="branding-section hidden-sm-and-down">
      <img
        :src="loginBackground"
        class="background-image"
        alt="Capstone Project"
      >
      <div class="branding-overlay" />
      <!-- <div class="branding-content">
        <div class="brand-logo-wrapper">
          <img
            :src="logo"
            class="brand-logo"
            alt="Capstone Project"
          >
        </div>
        <div class="brand-title">
          <h1>A-POS</h1>
          <div class="brand-line" />
          <p>
            Digital Disposition Management System
          </p>
        </div>

        <div class="brand-description">
          <p>
            Dashboard manajemen website Capstone Project
          </p>
        </div>
      </div> -->

      <!-- <div class="branding-footer">
        © Copyright 2026. All Rights Reserved
        <br>
        <a
          href="https://www.bprdjokotingkir.com/"
          target="_blank"
          rel="noopener noreferrer"
        >
          Capstone Project Kabupaten Sragen
        </a>
        <br>
        Designed by
        <strong>Team IT Capstone Project</strong>
      </div> -->
    </div>

    <!-- =========================
         RIGHT / LOGIN SECTION
    ========================== -->
    <div
      class="login-section"
    >
      <div class="login-background-overlay" />
      <div class="login-wrapper">
        <el-card
          class="login-card"
          :body-style="{ padding: '0' }"
          shadow="never"
        >
          <!-- Mobile Logo -->
          <div class="mobile-logo">
            <img
              :src="logo"
              alt="Capstone Project"
            >
          </div>
          <!-- =========================
               HEADER
          ========================== -->
          <div class="login-header">
            <div class="welcome-text">
              Selamat Datang
            </div>
            <h2>
              A-POS
            </h2>
            <div class="header-line" />
            <p>
              Capstone Project Kelompok A Kelas 144
            </p>
          </div>

          <!-- =========================
               FORM
          ========================== -->
          <form
            ref="loginForm"
            class="login-form"
            @submit.prevent="handleLogin"
          >
            <!-- EMAIL -->
            <div class="form-group">
              <label>
                Email
              </label>
              <div class="input-wrapper">
                <span class="input-icon">
                  <svg-icon icon-class="user" />
                </span>
                <input
                  v-model="loginForm.email"
                  required
                  type="email"
                  autocomplete="email"
                  name="email"
                  placeholder="Masukkan email Anda"
                  class="form-input"
                >
              </div>
            </div>
            <!-- PASSWORD -->
            <div class="form-group">
              <label>
                Password
              </label>
              <div class="input-wrapper">
                <span class="input-icon">
                  <svg-icon icon-class="password" />
                </span>
                <input
                  v-model="loginForm.password"
                  :type="pwdType"
                  required
                  autocomplete="current-password"
                  name="password"
                  placeholder="Masukkan password Anda"
                  class="form-input password-input"
                >
                <span
                  class="password-toggle"
                  @click="showPwd"
                >
                  <svg-icon
                    :icon-class="
                      pwdType === 'password'
                        ? 'eye'
                        : 'eye-open'
                    "
                  />
                </span>
              </div>
            </div>
            <!-- LOGIN BUTTON -->
            <button
              type="submit"
              class="login-button"
              :class="{ 'is-loading': loading }"
              :disabled="loading"
            >
              <span
                v-if="!loading"
                class="button-content"
              >
                <span>Masuk ke Sistem</span>
                <i class="el-icon-arrow-right" />
              </span>
              <span
                v-else
                class="loading-content"
              >
                <i class="el-icon-loading" />
                Memproses...
              </span>
            </button>
          </form>

          <!-- =========================
               SECURITY INFO
          ========================== -->
          <!-- <div class="security-info">
            <span class="security-icon">
              <i class="el-icon-lock" />
            </span>
            <div>
              <strong>Sistem Terproteksi</strong>
              <span>
                Data dan akses Anda dilindungi
              </span>
            </div>
          </div> -->
          <!-- =========================
               MOBILE FOOTER
          ========================== -->
          <div class="mobile-footer">
            © 2026 Capstone Project Kelompok A Kelas 144
            <br>
            <strong>
              S1 Sistem Informasi UT
            </strong>
          </div>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script>
import 'element-ui/lib/theme-chalk/display.css';
import { validEmail } from '@/utils/validate';
import { csrf } from '@/api/auth';

import logo from '@/assets/login/logo_capstone_project.png';
import logo_djuara from '@/assets/login/djuara.png';
import loginBackground from '@/assets/login/bg_ut_cp.png';
import batik from '@/assets/login/batik.png';
import batik_biru from '@/assets/login/batik_biru.png';

export default {
  name: 'Login',
  data() {
    const validateEmail = (rule, value, callback) => {
      if (!validEmail(value)) {
        callback(new Error('Please enter the correct email'));
      } else {
        callback();
      }
    };
    const validatePass = (rule, value, callback) => {
      if (value.length < 4) {
        callback(new Error('Password cannot be less than 4 digits'));
      } else {
        callback();
      }
    };
    return {
      loginForm: {
        email: '',
        password: '',
      },
      loginRules: {
        email: [
          {
            required: true,
            trigger: 'blur',
            validator: validateEmail,
          },
        ],
        password: [
          {
            required: true,
            trigger: 'blur',
            validator: validatePass,
          },
        ],
      },
      loading: false,
      pwdType: 'password',
      redirect: undefined,
      otherQuery: {},
      logo: logo,
      batik: batik,
      batik_biru: batik_biru,
      logo_djuara: logo_djuara,
      loginBackground: loginBackground,
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        const query = route.query;
        if (query) {
          this.redirect = query.redirect;

          this.otherQuery = this.getOtherQuery(query);
        }
      },
      immediate: true,
    },
  },
  methods: {
    // =========================
    // SHOW / HIDE PASSWORD
    // =========================
    showPwd() {
      if (this.pwdType === 'password') {
        this.pwdType = 'text';
      } else {
        this.pwdType = 'password';
      }
    },

    // =========================
    // LOGIN
    // =========================
    handleLogin() {
      if (
        this.loginForm.email &&
        this.loginForm.password &&
        validEmail(this.loginForm.email) &&
        this.loginForm.password.length >= 4
      ) {
        this.loading = true;
        csrf()
          .then(() => {
            return this.$store.dispatch(
              'user/login',
              this.loginForm
            );
          })
          .then(() => {
            this.$router.push({
              path: this.redirect || '/',
              query: this.otherQuery,
            });
            this.loading = false;
          })
          .catch(() => {
            this.loading = false;
          });
      } else {
        this.$message.error(
          'Email atau Password tidak tepat'
        );
      }
    },

    // =========================
    // GET QUERY
    // =========================
    getOtherQuery(query) {
      return Object.keys(query).reduce(
        (acc, cur) => {
          if (cur !== 'redirect') {
            acc[cur] = query[cur];
          }
          return acc;
        },
        {}
      );
    },

    loginBackgroundImage() {
      return 'url(\'' + this.loginBackground + '\')';
    },
  },
};
</script>

<style>
/* ==========================================================
   GLOBAL
========================================================== */

html,
body,
#app {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
}

body {
  overflow: hidden;
}

/* ==========================================================
   MAIN PAGE
========================================================== */

.login-page {
  width: 100%;
  height: 100vh;
  display: flex;
  overflow: hidden;
  background: #f5f7fb;
}

/* ==========================================================
   LEFT BRANDING
========================================================== */

.branding-section {
  position: relative;
  width: 58%;
  height: 100vh;
  overflow: hidden;
  background: rgb(224, 235, 246);
}

.background-image {
  position: absolute;
  inset: 0;

  width: 100%;
  height: 100%;

  object-fit: cover;

  transition: transform 8s ease;
}

.branding-section:hover .background-image {
  transform: scale(1);
}

/* Overlay */

.branding-overlay {
  position: absolute;
  inset: 0;

  /* background:
    linear-gradient(
      135deg,
      rgba(35, 63, 130, 0.90) 0%,
      rgba(35, 63, 130, 0.60) 45%,
      rgba(0, 0, 0, 0.25) 100%
    ); */
}

/* ==========================================================
   BRANDING CONTENT
========================================================== */

.branding-content {
  position: absolute;

  left: 10%;
  top: 50%;

  transform: translateY(-50%);

  width: 70%;

  color: #ffffff;

  animation: fadeInLeft 1s ease;
}

.brand-logo-wrapper {
  margin-bottom: 30px;
}

.brand-logo {
  width: 210px;
  max-width: 70%;

  height: auto;

  object-fit: contain;

  filter: drop-shadow(
    0 10px 20px rgba(0, 0, 0, 0.20)
  );
}

.brand-title h1 {
  margin: 0;

  font-size: 42px;

  font-weight: 800;

  letter-spacing: 1px;

  line-height: 1.2;
}

.brand-line {
  width: 75px;
  height: 4px;

  margin: 20px 0;

  border-radius: 10px;

  background: #ffffff;
}

.brand-title p {
  margin: 0;

  font-size: 18px;

  font-weight: 500;

  letter-spacing: 0.4px;

  opacity: 0.95;
}

.brand-description {
  max-width: 550px;

  margin-top: 25px;
}

.brand-description p {
  margin: 0;

  font-size: 15px;

  line-height: 1.7;

  opacity: 0.82;
}

/* ==========================================================
   BRANDING FOOTER
========================================================== */

.branding-footer {
  position: absolute;

  left: 8%;
  right: 8%;
  bottom: 25px;

  text-align: center;

  color: rgba(255, 255, 255, 0.85);

  font-size: 11px;

  line-height: 1.7;
}

.branding-footer a {
  color: #ffffff;

  text-decoration: none;

  font-weight: 600;
}

.branding-footer strong {
  color: #ffffff;
}

/* ==========================================================
   RIGHT LOGIN
========================================================== */

.login-section {
  position: relative;

  width: 42%;
  height: 100vh;

  display: flex;

  justify-content: center;
  align-items: center;

  background-color: #f8faff;

  background-size: 280px;
}

.login-background-overlay {
  position: absolute;

  inset: 0;

  background:
    linear-gradient(
      135deg,
      rgba(255, 255, 255, 0.94),
      rgba(248, 250, 255, 0.90)
    );
}

/* ==========================================================
   LOGIN WRAPPER
========================================================== */

.login-wrapper {
  position: relative;

  width: 100%;

  display: flex;

  justify-content: center;

  align-items: center;

  padding: 30px;

  box-sizing: border-box;

  animation: fadeIn 0.8s ease;
}

/* ==========================================================
   LOGIN CARD
========================================================== */

.login-card {
  width: 430px;

  max-width: 100%;

  padding: 42px 45px 32px;

  box-sizing: border-box;

  border: 1px solid rgba(35, 63, 130, 0.08);

  border-radius: 22px;

  background: rgba(255, 255, 255, 0.97);

  box-shadow:
    0 5px 10px rgba(35, 63, 130, 0.12),
    0 5px 15px rgba(0, 0, 0, 0.04);
}

/* ==========================================================
   HEADER
========================================================== */

.login-header {
  text-align: center;

  margin-bottom: 30px;
}

.welcome-text {
  /* color: #8892a6; */

  font-size: 13px;

  font-weight: 500;

  margin-bottom: 8px;
}

.login-header h2 {
  margin: 0;

  color: #233f82;

  font-size: 21px;

  font-weight: 800;

  letter-spacing: 0.5px;
}

.header-line {
  width: 45px;

  height: 3px;

  margin: 13px auto;

  border-radius: 10px;

  background: #3c3897;
}

.login-header p {
  margin: 0;

  /* color: #8992a5; */

  font-size: 12px;

  line-height: 1.6;

  font-weight: 500;
}

/* ==========================================================
   FORM
========================================================== */

.login-form {
  width: 100%;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;

  margin-bottom: 8px;

  color: #3b4354;

  font-size: 13px;

  font-weight: 600;
}

.input-wrapper {
  position: relative;

  width: 100%;

  height: 50px;

  display: flex;

  align-items: center;

  box-sizing: border-box;

  border: 1px solid #e2e6ee;

  border-radius: 10px;

  background: #ffffff;

  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease,
    transform 0.2s ease;
}

.input-wrapper:focus-within {
  /* border-color: #233f82; */

  box-shadow:
    0 0 0 3px rgba(35, 63, 130, 0.08);

  transform: translateY(-1px);
}

.input-icon {
  width: 48px;

  height: 100%;

  display: flex;

  align-items: center;

  justify-content: center;

  color: #8a94a7;

  font-size: 17px;
}

.form-input {
  flex: 1;

  min-width: 0;

  height: 100%;

  padding: 0 45px 0 0;

  border: 0;

  outline: none;

  background: transparent;

  color: #2d3445;

  font-size: 14px;

  font-family:
    'Segoe UI',
    Tahoma,
    Geneva,
    Verdana,
    sans-serif;
}

.form-input::placeholder {
  color: #b3bac8;
}

.form-input:-webkit-autofill {
  -webkit-box-shadow:
    0 0 0 1000px #ffffff inset !important;

  -webkit-text-fill-color: #2d3445 !important;

  -webkit-border: 0 !important;

  -webkit-border-radius: 10px !important;
}

.password-toggle {
  position: absolute;

  right: 15px;

  top: 50%;

  transform: translateY(-50%);

  color: #9aa3b3;

  font-size: 16px;

  cursor: pointer;

  transition: color 0.2s ease;
}

.password-toggle:hover {
  color: #233f82;
}

/* ==========================================================
   LOGIN BUTTON
========================================================== */

.login-button {
  position: relative;

  width: 100%;

  height: 52px;

  margin-top: 5px;

  border: 0;

  border-radius: 10px;

  cursor: pointer;

  color: #ffffff;

  font-size: 14px;

  font-weight: 700;

  letter-spacing: 0.2px;

  background:
    linear-gradient(
      135deg,
      #233f82 0%,
      #3c3897 100%
    );

  box-shadow:
    0 8px 20px rgba(35, 63, 130, 0.20);

  transition:
    transform 0.2s ease,
    box-shadow 0.2s ease,
    opacity 0.2s ease;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-2px);

  box-shadow:
    0 12px 25px rgba(35, 63, 130, 0.28);
}

.login-button:active:not(:disabled) {
  transform: translateY(0);
}

.login-button:disabled {
  cursor: not-allowed;

  opacity: 0.75;
}

.button-content {
  display: flex;

  align-items: center;

  justify-content: center;

  gap: 10px;
}

.button-icon {
  font-size: 15px;
}

.loading-content {
  display: flex;

  align-items: center;

  justify-content: center;

  gap: 9px;
}

/* ==========================================================
   SECURITY INFO
========================================================== */

.security-info {
  display: flex;

  align-items: center;

  justify-content: center;

  gap: 10px;

  margin-top: 25px;

  padding: 12px;

  border-radius: 9px;

  background: #f7f9fc;

  color: #7e8799;
}

.security-icon {
  width: 32px;

  height: 32px;

  display: flex;

  justify-content: center;

  align-items: center;

  border-radius: 50%;

  color: #233f82;

  background: rgba(35, 63, 130, 0.08);

  font-size: 14px;
}

.security-info div {
  display: flex;

  flex-direction: column;

  gap: 2px;
}

.security-info strong {
  color: #4d5668;

  font-size: 11px;
}

.security-info span {
  font-size: 10px;
}

/* ==========================================================
   MOBILE
========================================================== */

.mobile-logo {
  text-align: center;

  margin-bottom: 20px;
}

.mobile-logo img {
  width: 180px;

  max-width: 80%;

  height: auto;
}

/* ==========================================================
   MOBILE FOOTER
========================================================== */

.mobile-footer {
  margin-top: 25px;

  text-align: center;

  /* color: #9aa2b1; */

  font-size: 9px;

  line-height: 1.8;
}

.mobile-footer strong {
  color: #233f82;
}

/* ==========================================================
   ANIMATION
========================================================== */

@keyframes fadeIn {

  from {
    opacity: 0;

    transform: translateY(20px);
  }

  to {
    opacity: 1;

    transform: translateY(0);
  }

}

@keyframes fadeInLeft {

  from {
    opacity: 0;

    transform:
      translate(-30px, -50%);
  }

  to {
    opacity: 1;

    transform:
      translate(0, -50%);
  }

}

/* ==========================================================
   TABLET
========================================================== */

@media screen and (max-width: 1199px) {

  .branding-section {
    width: 53%;
  }

  .login-section {
    width: 47%;
  }

  .branding-content {
    left: 8%;
    width: 80%;
  }

  .brand-title h1 {
    font-size: 34px;
  }

  .login-card {
    width: 390px;

    padding:
      38px 35px 30px;
  }

}

/* ==========================================================
   MOBILE
========================================================== */

@media screen and (max-width: 767px) {

  body {
    overflow: auto;
  }

  .login-page {
    min-height: 100vh;

    height: auto;

    display: block;

    background:
      linear-gradient(
        135deg,
        #f8faff,
        #ffffff
      );
  }

  .login-section {
    width: 100%;

    min-height: 100vh;

    height: auto;

    background-size: 200px;

    display: flex;

    align-items: center;
  }

  .login-wrapper {
    min-height: 100vh;

    padding:
      25px 18px;
  }

  .login-card {
    width: 100%;

    max-width: 430px;

    padding:
      30px 24px 25px;

    border-radius: 18px;

    box-shadow:
      0 12px 40px rgba(35, 63, 130, 0.10);
  }

  .login-header {
    margin-bottom: 25px;
  }

  .login-header h2 {
    font-size: 23px;
  }

  .login-button {
    height: 50px;
  }

}

/* ==========================================================
   SMALL MOBILE
========================================================== */

@media screen and (max-width: 359px) {

  .login-wrapper {
    padding:
      15px 12px;
  }

  .login-card {
    padding:
      25px 18px 20px;
  }

  .login-header h2 {
    font-size: 21px;
  }

  .form-group {
    margin-bottom: 16px;
  }

}
</style>


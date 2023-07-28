export interface LoginData {
  login: string
  password: string
}

export interface TwoFactorAuthData {
  code: string,
  email: string | null
}

export interface ResetPasswordData {
  login: string
}

export interface RestorePasswordData {
  password: string
  confirm: string
  token: string
}

export interface CheckResetPasswordTokenData {
  token: string
}

export interface SignUpData {
  email: string
  tagname: string
  username: string
  password: string
  confirm: string
  redirect_after_verification: string
}

export interface VerifyData {
  token: string
}
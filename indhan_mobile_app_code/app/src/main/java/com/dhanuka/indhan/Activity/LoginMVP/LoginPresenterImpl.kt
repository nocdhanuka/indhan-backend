package com.dhanuka.indhan.Activity.LoginMVP

import com.dhanuka.indhan.Activity.LoginMVP.Model.LoginBean
import java.util.*

/**
 * Created by Shivam on 2/26/2018.
 */

class LoginPresenterImpl(private val mILoginView: LoginContracts.ILoginView?) :
    LoginContracts.LoginPresenter, LoginContracts.LoginInteractor.OnFinishedListener {

    private val mLoginInteractor: LoginContracts.LoginInteractor

    init {
        this.mLoginInteractor = LoginInteractorImpl()
    }


    override fun onSubmitClick(mLoginRequest: HashMap<String, Any>) {
        mILoginView?.showProgress()
        mLoginInteractor.userLogin(this, mLoginRequest)
    }

    override fun onLoginSucess(items: LoginBean) {
        if (mILoginView != null) {
            mILoginView.setItems(items)
            mILoginView.hideProgress()
        }
    }

    override fun onLoginFailed(errorMessage: String) {
        mILoginView!!.hideProgress()
        mILoginView!!.failure(errorMessage)
    }
}

package com.dhanuka.indhan.Activity.LogoutMVP

import com.dhanuka.indhan.Activity.LogoutMVP.Model.LogoutBean

/**
 * Created by Shivam on 2/27/2018.
 */

class LogoutPresenterImpl(private val mILogoutView: LogoutContracts.ILogoutView?) :
    LogoutContracts.LogoutPresenter, LogoutContracts.LogoutInteractor.OnFinishedListener {
    private val mLogoutInteractor: LogoutContracts.LogoutInteractor

    init {
        this.mLogoutInteractor = LogoutInteractorImpl()
    }


    override fun onSubmitClick(mLogoutRequest: String) {
        mILogoutView!!.showProgress()
        mLogoutInteractor.userLogout(this, mLogoutRequest)
    }

    override fun onLogoutSucess(items: LogoutBean) {
        if (mILogoutView != null) {
            mILogoutView.setItems(items)
            mILogoutView.hideProgress()
        }
    }

    override fun onLogoutFailed(errorMessage: String) {}
}

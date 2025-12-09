package com.dhanuka.indhan.Activity.LogoutMVP

import com.dhanuka.indhan.Activity.LogoutMVP.Model.LogoutBean

/**
 * Created by Shivam on 2/27/2018.
 */

interface LogoutContracts {

    interface ILogoutView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: LogoutBean)
    }

    interface LogoutPresenter {

        fun onSubmitClick(mLogoutRequest: String)
    }

    interface LogoutInteractor {

        fun userLogout(listener: OnFinishedListener, model: String)

        interface OnFinishedListener {
            fun onLogoutSucess(items: LogoutBean)

            fun onLogoutFailed(errorMessage: String)
        }
    }
}

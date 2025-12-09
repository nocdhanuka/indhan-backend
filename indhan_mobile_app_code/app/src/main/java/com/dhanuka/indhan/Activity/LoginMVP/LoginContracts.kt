package com.dhanuka.indhan.Activity.LoginMVP

import com.dhanuka.indhan.Activity.LoginMVP.Model.LoginBean
import java.util.*

/**
 * Created by Shivam on 2/26/2018.
 */

interface LoginContracts {

    interface ILoginView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: LoginBean)
        fun failure(items: String)
    }

    interface LoginPresenter {

        fun onSubmitClick(mLoginRequest: HashMap<String, Any>)
    }

    interface LoginInteractor {

        fun userLogin(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onLoginSucess(items: LoginBean)

            fun onLoginFailed(errorMessage: String)
        }
    }
}

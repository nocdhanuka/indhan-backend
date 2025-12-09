package com.dhanuka.indhan.Profile

import java.util.*

interface ProfileContractor {

    interface ProfileView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: DDProfileBean)
        fun failure(items: String)
    }

    interface ProfilePresenter {

        fun onGetProfile(mProfile: HashMap<String, Any>)
    }

    interface ProfileInteractor {

        fun ddProfile(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onProfileSuccess(items: DDProfileBean)

            fun onProfileFailed(errorMessage: String)
        }
    }
}
package com.dhanuka.indhan.ATPMenu.activity

import java.util.*

interface ATPMenuContractor {
    interface AddFarmerView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: ATPBean)
        fun setClosedATP(items: ClosedATPBean)

        fun failure(items: String)
    }

    interface ATPMenuPresenter {

        fun onATPMenu(mProfile: HashMap<String, Any>)
        fun onClosedATP(mProfile: HashMap<String, Any>)

    }

    interface ATPMenuInteractor {
        fun onATPMenu(listener: OnFinishedListener, model: HashMap<String, Any>)
        fun onClosedATP(listener: OnFinishedListener, model: HashMap<String, Any>)


        interface OnFinishedListener {
            fun onATPMenuSuccess(items: ATPBean)
            fun onCloseATPSuccess(items: ClosedATPBean)
            fun onATPMenuFailed(errorMessage: String)
        }
    }

}
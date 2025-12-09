package com.dhanuka.indhan.validatefarmers

import java.util.*

interface ValidateFarmerContractor {
    interface ValidateFarmerView {
        fun showProgress()

        fun hideProgress()

        fun setFarmerList(items: ValidatedFarmerBean)
        fun setCall(items: SuccessBean)
        fun setChangeStatus(items: SuccessBean)

        fun failure(items: String)
    }


    interface ValidateFarmerPresenter {

        fun getFarmerList(mFarmer: HashMap<String, Any>)
        fun onCallFarmer(mParam: HashMap<String, Any>)
        fun onMakeLead(mLead: HashMap<String, Any>)


    }

    interface TestimonialListInteractor {

        fun getFarmerList(listener: OnFinishedListener, mFarmer: HashMap<String, Any>)
        fun onCallFarmer(listener: OnFinishedListener, mParam: HashMap<String, Any>)
        fun onMakeLead(listener: OnFinishedListener, mLead: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onFarmerListSuccess(items: ValidatedFarmerBean)
            fun onCallSuccess(items: SuccessBean)
            fun onStatusChangeSuccess(items: SuccessBean)


            fun onFailed(errorMessage: String)
        }
    }
}
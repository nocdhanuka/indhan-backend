package com.dhanuka.indhan.validatefarmers

import java.util.*

class ValidatefarmerPresenter(val view: ValidateFarmerContractor.ValidateFarmerView) :
    ValidateFarmerContractor.ValidateFarmerPresenter,
    ValidateFarmerContractor.TestimonialListInteractor.OnFinishedListener {
    override fun onCallSuccess(items: SuccessBean) {
        view.hideProgress()
        view.setCall(items)
    }

    override fun onStatusChangeSuccess(items: SuccessBean) {
        view.hideProgress()
        view.setChangeStatus(items)

    }

    lateinit var mIntractor: ValidateFarmerContractor.TestimonialListInteractor

    init {

        mIntractor = ValidateFarmerIntractor()
    }

    override fun getFarmerList(mFarmer: HashMap<String, Any>) {
        view.showProgress()
        mIntractor.getFarmerList(this, mFarmer)
    }

    override fun onCallFarmer(mParam: HashMap<String, Any>) {
        view.showProgress()
        mIntractor.onCallFarmer(this, mParam)
    }

    override fun onMakeLead(mLead: HashMap<String, Any>) {
        view.showProgress()
        mIntractor.onMakeLead(this, mLead)
    }

    override fun onFarmerListSuccess(items: ValidatedFarmerBean) {
        view.hideProgress()
        view.setFarmerList(items)
    }

    override fun onFailed(errorMessage: String) {
        view.hideProgress()
        view.failure(errorMessage)
    }
}
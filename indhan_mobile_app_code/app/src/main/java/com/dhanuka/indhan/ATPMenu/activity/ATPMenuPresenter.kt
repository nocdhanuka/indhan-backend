package com.dhanuka.indhan.ATPMenu.activity

import java.util.*

class ATPMenuPresenter(var view: ATPMenuContractor.AddFarmerView?) :
    ATPMenuContractor.ATPMenuPresenter, ATPMenuContractor.ATPMenuInteractor.OnFinishedListener {
    override fun onClosedATP(mProfile: HashMap<String, Any>) {
        mIntractor.onClosedATP(this, mProfile)
    }

    override fun onCloseATPSuccess(items: ClosedATPBean) {
        view?.hideProgress()
        view?.setClosedATP(items)
    }

    override fun onATPMenuSuccess(items: ATPBean) {
        view?.hideProgress()
        view?.setItems(items)
    }

    lateinit var mIntractor: ATPMenuContractor.ATPMenuInteractor

    init {
        mIntractor = ATPMenuIntractor()
    }

    override fun onATPMenu(mProfile: HashMap<String, Any>) {
        view?.showProgress()
        mIntractor.onATPMenu(this, mProfile)
    }


    override fun onATPMenuFailed(errorMessage: String) {
        view?.hideProgress()
        view?.failure(errorMessage)
    }
}
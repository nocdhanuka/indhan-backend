package com.dhanuka.indhan.Profile


import java.util.*

class ProfilePresenterImpl(private val view: ProfileContractor.ProfileView?) :
    ProfileContractor.ProfilePresenter, ProfileContractor.ProfileInteractor.OnFinishedListener {

    private val mProfileInteractor: ProfileContractor.ProfileInteractor

    init {
        this.mProfileInteractor = ProfileInteractorImpl()
    }

    override fun onGetProfile(mProfile: HashMap<String, Any>) {
        view?.showProgress()
        mProfileInteractor.ddProfile(this, mProfile)
    }

    override fun onProfileSuccess(items: DDProfileBean) {
        if (view != null) {
            view.setItems(items)
            view.hideProgress()
        }
    }

    override fun onProfileFailed(errorMessage: String) {
        view?.failure(errorMessage)
        view?.hideProgress()

    }
}
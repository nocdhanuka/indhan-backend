package com.dhanuka.indhan.Activity.HomeMVP

import com.dhanuka.indhan.Activity.HomeMVP.Model.HomeBean

/**
 * Created by Shivam on 2/27/2018.
 */

class HomePresenterImpl(private val mIHomeView: HomeContracts.IHomeView?) :
    HomeContracts.HomePresenter, HomeContracts.HomeInteractor.OnFinishedListener {
    private val mHomeInteractor: HomeContracts.HomeInteractor

    init {
        this.mHomeInteractor = HomeInteractorImpl()
    }


    override fun onSubmitClick(mHomeRequest: String) {
        mIHomeView?.showProgress()
        mHomeInteractor.userHome(this, mHomeRequest)
    }

    override fun onHomeSucess(items: HomeBean) {
        if (mIHomeView != null) {
            mIHomeView.setItems(items)
            mIHomeView.hideProgress()
        }
    }

    override fun onHomeFailed(errorMessage: String) {}
}

package com.dhanuka.indhan.Activity.RegisterFarmersMVP

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.RegisterFarmersBean

/**
 * Created by Shivam on 3/8/2018.
 */

class RegisterFarmersPresenterImpl(private val mIRegisterFarmersView: RegisterFarmersContracts.IRegisterFarmersView?) :
    RegisterFarmersContracts.RegisterFarmersPresenter,
    RegisterFarmersContracts.RegisterFarmersInteractor.OnFinishedListener {
    private val mRegisterFarmersInteractor: RegisterFarmersContracts.RegisterFarmersInteractor

    init {
        this.mRegisterFarmersInteractor = RegisterFarmersInteractorImpl()
    }

    override fun onSubmitClick(mRegisterFarmersRequest: String) {
        mIRegisterFarmersView?.showProgress()
        mRegisterFarmersInteractor.userRegisterFarmers(this, mRegisterFarmersRequest)
    }

    override fun onRegisterFarmersSucess(items: RegisterFarmersBean) {
        if (mIRegisterFarmersView != null) {
            mIRegisterFarmersView.setItems(items)
            mIRegisterFarmersView.hideProgress()
        }
    }

    override fun onRegisterFarmersFailed(errorMessage: String) {}
}

package com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP

import com.dhanuka.indhan.Model.CommonModel
import com.dhanuka.indhan.Model.RegisterFarmerContactModel
import java.util.*

/**
 * Created by Shivam on 3/9/2018.
 */

class SubmitFarmersPresenterImpl(private val mISubmitFarmersView: SubmitFarmersContracts.ISubmitFarmersView?) :
    SubmitFarmersContracts.SubmitFarmersPresenter,
    SubmitFarmersContracts.SubmitFarmersInteractor.OnFinishedListener {
    private val mSubmitFarmersInteractor: SubmitFarmersContracts.SubmitFarmersInteractor

    init {
        this.mSubmitFarmersInteractor = SubmitFarmersInteractorImpl()
    }


    override fun onSubmitClick(mSubmitFarmersRequest: HashMap<String, Any>) {
//        mISubmitFarmersView?.showProgress()
//        mSubmitFarmersInteractor.userSubmitFarmers(this, mSubmitFarmersRequest)
    }

    override fun onSubmitClickOptimized(
        mSubmitFarmersRequest: ArrayList<RegisterFarmerContactModel>,
        token: String
    ) {
        mISubmitFarmersView?.showProgress()
        mSubmitFarmersInteractor.userSubmitFarmersOptimized(this, mSubmitFarmersRequest, token)
    }

    override fun onSubmitFarmersSucess(items: CommonModel) {
        if (mISubmitFarmersView != null) {
            mISubmitFarmersView.setItems(items)
            mISubmitFarmersView.hideProgress()
        }
    }

    override fun onSubmitFarmersFailed(errorMessage: String) {
        if (mISubmitFarmersView != null)
            mISubmitFarmersView.onFailureFarmers(errorMessage)
    }
}

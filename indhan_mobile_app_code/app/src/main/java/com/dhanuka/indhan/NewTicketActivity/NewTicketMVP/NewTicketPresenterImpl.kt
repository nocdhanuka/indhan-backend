package com.dhanuka.indhan.Activity.NewTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.Model.NewTicketBean

/**
 * Created by Shivam on 2/28/2018.
 */

class NewTicketPresenterImpl(private val mINewTicketView: NewTicketContracts.INewTicketView?) :
    NewTicketContracts.NewTicketPresenter,
    NewTicketContracts.NewTicketInteractor.OnFinishedListener {
    private val mNewTicketInteractor: NewTicketContracts.NewTicketInteractor

    init {
        this.mNewTicketInteractor = NewTicketInteractorImpl()
    }


    override fun onSubmitClick(mNewTicketRequest: String) {
        mINewTicketView!!.showProgress()
        mNewTicketInteractor.userNewTicket(this, mNewTicketRequest)
    }

    override fun onNewTicketSucess(items: NewTicketBean) {
        if (mINewTicketView != null) {
            mINewTicketView.setItems(items)
            mINewTicketView.hideProgress()
        }
    }

    override fun onNewTicketFailed(errorMessage: String) {
        if (mINewTicketView != null) {
            mINewTicketView.hideProgress()
            mINewTicketView.onFailure("Server Not Responding")

        }
    }
}

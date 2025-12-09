package com.dhanuka.indhan.Activity.TicketHistoryMVP

import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel

/**
 * Created by Shivam on 3/6/2018.
 */

class TicketHistoryPresenterImpl(private val mITicketHistoryView: TicketHistoryContracts.ITicketHistoryView?) :
    TicketHistoryContracts.TicketHistoryPresenter,
    TicketHistoryContracts.TicketHistoryInteractor.OnFinishedListener {
    private val mTicketHistoryInteractor: TicketHistoryContracts.TicketHistoryInteractor

    init {
        this.mTicketHistoryInteractor = TicketHistoryInteractorImpl()
    }


    override fun onSubmitClick(
        mTicketHistoryRequest: String,
        lastTicketID: String,
        offset: String,
        isProgress: Boolean
    ) {
        if (isProgress) {
            mITicketHistoryView?.showProgress()
        }
        mTicketHistoryInteractor.userTicketHistory(
            this,
            mTicketHistoryRequest,
            lastTicketID,
            offset
        )
    }

    override fun onTicketHistorySucess(items: TicketHistoryModel) {
        if (mITicketHistoryView != null) {
            mITicketHistoryView.setItems(items)
            mITicketHistoryView.hideProgress()
        }
    }

    override fun onTicketHistoryFailed(errorMessage: String) {
        if (mITicketHistoryView != null) {
            mITicketHistoryView.hideProgress()
        }
    }
}

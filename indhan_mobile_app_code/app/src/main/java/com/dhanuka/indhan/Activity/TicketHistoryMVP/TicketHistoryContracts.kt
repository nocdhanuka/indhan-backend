package com.dhanuka.indhan.Activity.TicketHistoryMVP

import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel

/**
 * Created by Shivam on 3/6/2018.
 */

interface TicketHistoryContracts {

    interface ITicketHistoryView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: TicketHistoryModel)
    }

    interface TicketHistoryPresenter {

        fun onSubmitClick(
            mTicketHistoryRequest: String,
            lastTicketID: String,
            offset: String,
            isProgress: Boolean
        )
    }

    interface TicketHistoryInteractor {

        fun userTicketHistory(
            listener: OnFinishedListener,
            model: String,
            ticketId: String,
            offset1: String
        )

        interface OnFinishedListener {
            fun onTicketHistorySucess(items: TicketHistoryModel)

            fun onTicketHistoryFailed(errorMessage: String)
        }
    }
}

package com.dhanuka.indhan.Activity.NewTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.Model.NewTicketBean

/**
 * Created by Shivam on 2/28/2018.
 */

interface NewTicketContracts {

    interface INewTicketView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: NewTicketBean)

        fun onFailure(items: String)

    }

    interface NewTicketPresenter {

        fun onSubmitClick(mNewTicketRequest: String)
    }

    interface NewTicketInteractor {

        fun userNewTicket(listener: OnFinishedListener, model: String)

        interface OnFinishedListener {
            fun onNewTicketSucess(items: NewTicketBean)

            fun onNewTicketFailed(errorMessage: String)
        }
    }
}

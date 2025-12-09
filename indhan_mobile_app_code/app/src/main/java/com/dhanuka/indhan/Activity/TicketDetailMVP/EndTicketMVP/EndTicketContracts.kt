package com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP


import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.Model.EndTicketBean
import java.util.*

/**
 * Created by Shivam on 3/7/2018.
 */

interface EndTicketContracts {

    interface IEndTicketView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: EndTicketBean)
    }

    interface EndTicketPresenter {

        fun onSubmitClick(mEndTicketRequest: HashMap<String, Any>)
    }

    interface EndTicketInteractor {

        fun userEndTicket(
            listener: EndTicketContracts.EndTicketInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )

        interface OnFinishedListener {
            fun onEndTicketSucess(items: EndTicketBean)

            fun onEndTicketFailed(errorMessage: String)
        }
    }
}

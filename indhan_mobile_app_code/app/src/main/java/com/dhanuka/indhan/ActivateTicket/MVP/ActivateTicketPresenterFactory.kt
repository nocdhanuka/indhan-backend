package com.dhanuka.indhan.ActivateTicket.MVP

import com.dhanuka.indhan.ActivateTicket.Model.ActiveTicketListModel
import com.dhanuka.indhan.ActivateTicket.Model.PendingActiveTicketlistModel
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import java.util.*

/**
 * Created by John on 5/31/2018.
 */
interface ActivateTicketPresenterFactory {


    interface ActivityView {
        fun showProgress()
        fun hideProgress()
        fun onActivateTicketResponseSucess(token: ActiveTicketListModel)
        fun onActivateTicketResponseFailure(token: String)
    }

    interface ActivityATReasonView {
        fun showProgress()
        fun hideProgress()
        fun onActivateTicketResponseSucess(token: PendingActiveTicketlistModel)
        fun onActivateTicketResponseFailure(token: String)
        fun reasonResponse(token: String)
    }

    interface TicketDetailActivityView {
        fun showProgress()
        fun hideProgress()
        fun onActivateTicketResponseSucess(token: SubmitTicketBean)
        fun onActivateTicketResponseFailure(token: String)
    }

    interface ActivateTicketPresenter {
        fun onRequest()
        fun onRequestPreviousData(token: String)
        fun onActivateTicketResponseSucess(model: ActiveTicketListModel)
        fun onActivateTicketResponseFailure(msg: String)
    }

    interface ReasonActivateTicketPresenter {
        fun onRequestPreviousData(token: String)
        fun onActivateTicketResponseSucess(model: PendingActiveTicketlistModel)
        fun onActivateTicketResponseFailure(msg: String)
        fun reasonResponse(msg: String)
        fun reasonATP(token: String, id: String, reason: String)

    }


    interface TicketDetailPresenter {
        fun onActivateTicket(token: HashMap<String, Any>)
        fun onActivateTicketSubmitResponseSucess(model: SubmitTicketBean)
        fun onActivateTicketSubmitResponseFailure(model: String)
    }

}
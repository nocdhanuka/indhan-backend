package com.dhanuka.indhan.SelfParticipationActivity.SP_MVP

import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse
import com.dhanuka.indhan.SelfParticipationActivity.Model.PreviousPlanResponse
import com.dhanuka.indhan.SelfParticipationActivity.Model.SPDetailModel
import com.dhanuka.indhan.SelfParticipationActivity.Model.SelfParticipationResponse
import okhttp3.RequestBody

interface SelfParticipationFactory {

    interface HomeListener {
        fun showProgress()
        fun hideProgress()
        fun onSPDetailSuccess(model: SPDetailModel)
        fun onSuccess(model: SelfParticipationResponse)
        fun onPlanParticipationSuccess(model: ParticipationPlannedResponse)
        fun onPlanParticipationFailure(model: String)
        fun onFailure(mes: String)
    }

    interface SPDetailHomeListener {
        fun showProgress()
        fun hideProgress()
        fun onSuccess(model: String)
        fun onFailure(mes: String)
        fun onFailureParticipation(mes: String)
    }

    interface SPReasonHomeListener {
        fun showProgress()
        fun hideProgress()
        fun onSuccess(model: PreviousPlanResponse)
        fun onFailure(mes: String)
        fun reasonResponse(msg: String)
    }

    interface SPReasonPresenter {
        fun onRequestPreviousData(token: String)
        fun onPreviousPlanSuccess(message: PreviousPlanResponse)
        fun onPreviousPlanFailure(message: String)
        fun reasonSP(token: String, atp_it: String, reason: String)
        fun reasonResponse(message: String)
    }

    interface SelfParticipationPresenter {
        fun onRequestData(token: HashMap<String, Any>)
        fun onRequestDataSuccess(model: SelfParticipationResponse)
        fun onGetDetails(token: String, ticketId: String)
        fun onSPDetailSuccess(model: SPDetailModel)
        fun onRequestHistoryData(token: String)
        fun onFailure(token: String)

        fun planSelfParticipation(token: String, atp_it: String)
        fun onPlanParticipationSuccess(message: ParticipationPlannedResponse)
        fun onPlanParticipationFailure(message: String)
    }

    interface SelfParticipationDetailPresenter {
        fun onFailure(token: String)
        fun onFailureParticipation(token: String)
        fun onSubmitPhoto(model: HashMap<String, RequestBody>)
        fun onSubmitPhoto2(model: HashMap<String, RequestBody>)
        fun onSuccessUpload(response: ImageTicketResponse)
        fun onSuccessUploadParticipation(response: ImageTicketResponse)
    }


    interface SPDetailPresenter {
        fun onGetDetails(token: String, ticketId: String)
        fun onSuccess(obj: Any)
        fun onFailure(mesg: String)
    }

    interface DetailHomeListener {
        fun showProgress()
        fun hideProgress()
        fun onSuccess(model: Any)
        fun onFailure(mes: String)
    }


}
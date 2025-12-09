package com.dhanuka.indhan.DataSyncServices.Presenter

import com.dhanuka.indhan.ActivateTicket.MVP.ActivateTicketPresenterFactory
import com.dhanuka.indhan.Activity.HomeMVP.Model.HomeBean
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterFactory
import com.dhanuka.indhan.DataBase.TableDataModel.BDOPlanListDBModel
import com.dhanuka.indhan.DataBase.TableDataModel.BDOPlanListDetailDBModel
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DataSyncServices.Model.MySyllabusModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanActivityDataModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanRequirementDataModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanSyllabusModel
import com.dhanuka.indhan.Model.PlanATPApiModel
import com.dhanuka.indhan.Model.ProductSyllabusModel
import com.dhanuka.indhan.PlanATP.View.CustomerCareFactory
import com.dhanuka.indhan.PlanATP.presenter.ATPPresenter
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.Reports.Reports_MVP.ReportsFactory
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationFactory
import com.dhanuka.indhan.SyllabusHistory.Model.SharedSyllabusHistoryModel
import com.dhanuka.indhan.Testimonial.Testimonial_MVP.TestimonialFactory
import okhttp3.MultipartBody
import okhttp3.RequestBody
import java.util.*

/**
 * Created by John on 5/4/2018.
 */
interface SyncServiceInterfaceFactory {

    interface SyncDataManagerPresenter {
        fun onRequestDataResultSuccess(homeBean: HomeBean)
        fun onMySyllabusDataResultSuccess(homeBean: MySyllabusModel)
        fun onPlanActivityDataResultSuccess(homeBean: PlanActivityDataModel)
        fun onPlanSyllabusSuccess(homeBean: PlanSyllabusModel)
        fun onPlanRequirementSuccess(homeBean: PlanRequirementDataModel)
        fun getPLanBDOListOfS1Success(homeBean: BDOPlanListDBModel)
        fun getPLanBDODetailOfS1Success(homeBean: BDOPlanListDetailDBModel, id: Int)
        fun getProdCropSuccess(homeBean: ProductCropResponse)
        fun getPLanBDODetailOfS1Failure(homeBean: String)
        fun onRequestDataResultFailure()
        fun getSharedSyllabusSuccess(homeBean: SharedSyllabusHistoryModel)
    }

    interface SyncServiceInteractor {
        fun getMenuDataFromApi(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String
        )

        fun getMenuDataFromApi(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String,
            token: String,
            isMenu:Boolean
        )

        fun getProdCropList(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String
        )

        fun getMySyllabusData(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String,
            value: Int
        )

        fun getGetPlanActivityData(
            listener: SyncDataManagerPresenter,
            model: String,
            time: String
        )

        fun savePlanActivityData(
            listener: SyncServiceInterfaceFactory.SyllabusHistoryPresenter,
            model: ProductSyllabusModel
        )

        fun getPlanRequirement(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String
        )

        fun getPLanBDOListOfS1(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String
        )

        fun getPLanBDODetailOfS1(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String,
            id: Int
        )

        fun savePlanAtp(listener: ATPPresenter, model: PlanATPApiModel)
        fun getActiveTicketList(
            listener: ActivateTicketPresenterFactory.ActivateTicketPresenter,
            model: String
        )

        fun activateTicket(
            listener: ActivateTicketPresenterFactory.TicketDetailPresenter,
            model: HashMap<String, Any>
        )

        fun getSelfParticipationData(
            listener: SelfParticipationFactory.SelfParticipationPresenter,
            model: HashMap<String, Any>
        )

        fun getSelfParticipationHistoryData(
            listener: SelfParticipationFactory.SelfParticipationPresenter,
            model: String
        )

        fun getSelfParticipationPreviousData(
            listener: SelfParticipationFactory.SPReasonPresenter,
            model: String
        )

        fun getActiveTicketPreviousData(
            listener: ActivateTicketPresenterFactory.ReasonActivateTicketPresenter,
            model: String
        )

        fun submitActiveTicketReason(
            listener: ActivateTicketPresenterFactory.ReasonActivateTicketPresenter,
            model: HashMap<String, Any>
        )

        fun planSelfParticipation(
            listener: SelfParticipationFactory.SelfParticipationPresenter,
            model: HashMap<String, Any>
        )

        fun submitSelfParticipationReason(
            listener: SelfParticipationFactory.SPReasonPresenter,
            model: HashMap<String, Any>
        )

        fun submitgetSelfParticipationData(
            listener: SelfParticipationFactory.SelfParticipationDetailPresenter,
            model: HashMap<String, RequestBody>
        )

        fun submitgetImageUploadData(
            listener: SelfParticipationFactory.SelfParticipationDetailPresenter,
            model: HashMap<String, RequestBody>
        )

        fun submitTestimonialData(
            listener: TestimonialFactory.TestimonialPresenter,
            model: HashMap<String, RequestBody>,
            body: MultipartBody.Part
        )

        fun getReportsData(listener: ReportsFactory.ReportsPresenter, model: HashMap<String, Any>)
        fun getEfficiencyData(
            listener: ReportsFactory.ReportsPresenter,
            token: String,
            date: String
        )

        fun getProductList(listener: ReportsFactory.ProductListPresenter, model: String)
        fun getBDOFilterList(listener: BDOFilterFactory.BDOFilterPresenter, model: String)

        // Customer Care
        fun getCCQueries(listener: CustomerCareFactory.CCPresenter, model: String)
        fun getCCQueriesforS1(listener: CustomerCareFactory.CCSPresenter, model: String)
        fun submitCCRemarks(
            listener: CustomerCareFactory.CCQueryDetailPresenter,
            model: HashMap<String, Any>
        )

        fun submitCCForS1Remarks(
            listener: CustomerCareFactory.CCQueryDetailforS1Presenter,
            model: HashMap<String, Any>
        )

        //new Api SP
        fun getSPDeailsData(
            listener: SelfParticipationFactory.SelfParticipationPresenter,
            token: String,
            tid: String
        )

        fun getSharedSyllabusData(
            listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
            model: String
        )

    }

    interface FailureListener {
        fun onInvalidAttempt()
        fun onFailure(msg: String)
        fun onDBSyncSuccess()
    }

    interface SyllabusHistoryPresenter {
        fun onRequest(token: String, syllabus: ArrayList<SyllabusProduct>)
        fun onPlanSyllabusSuccess(homeBean: PlanSyllabusModel)
        fun onRequestDataResultFailure()
    }

    interface SyllabusHistoryView {
        fun onPlanSyllabusSuccess(homeBean: PlanSyllabusModel)
        fun showProgress()
        fun hideProgress()
    }
}
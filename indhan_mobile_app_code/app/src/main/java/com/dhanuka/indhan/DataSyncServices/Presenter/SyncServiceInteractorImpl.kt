package com.dhanuka.indhan.DataSyncServices.Presenter

import android.os.Handler
import com.dhanuka.indhan.ActivateTicket.MVP.ActivateTicketPresenterFactory
import com.dhanuka.indhan.ActivateTicket.Model.ActiveTicketListModel
import com.dhanuka.indhan.ActivateTicket.Model.PendingActiveTicketlistModel
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse
import com.dhanuka.indhan.Activity.HomeMVP.Model.HomeBean
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterFactory
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesForS1Model
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesModel
import com.dhanuka.indhan.DataBase.TableDataModel.BDOPlanListDBModel
import com.dhanuka.indhan.DataBase.TableDataModel.BDOPlanListDetailDBModel
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DataSyncServices.Model.*
import com.dhanuka.indhan.Model.PlanATPApiModel
import com.dhanuka.indhan.Model.ProductSyllabusModel
import com.dhanuka.indhan.PlanATP.View.CustomerCareFactory
import com.dhanuka.indhan.PlanATP.presenter.ATPPresenter
import com.dhanuka.indhan.Reports.Model.EfficiencyResponse
import com.dhanuka.indhan.Reports.Model.ProductListResponse
import com.dhanuka.indhan.Reports.Model.ReportsResponse
import com.dhanuka.indhan.Reports.Reports_MVP.ReportsFactory
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.NetworkClientRx
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse
import com.dhanuka.indhan.SelfParticipationActivity.Model.PreviousPlanResponse
import com.dhanuka.indhan.SelfParticipationActivity.Model.SPDetailModel
import com.dhanuka.indhan.SelfParticipationActivity.Model.SelfParticipationResponse
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationFactory
import com.dhanuka.indhan.SyllabusHistory.Model.SharedSyllabusHistoryModel
import com.dhanuka.indhan.Testimonial.Model.TestimonialResponse
import com.dhanuka.indhan.Testimonial.Testimonial_MVP.TestimonialFactory
import io.reactivex.Observer
import io.reactivex.disposables.Disposable
import io.reactivex.subjects.Subject
import okhttp3.MultipartBody
import okhttp3.RequestBody
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

/**
 * Created by John on 5/4/2018.
 */
class SyncServiceInteractorImpl : SyncServiceInterfaceFactory.SyncServiceInteractor {


    override fun getSPDeailsData(
        listener: SelfParticipationFactory.SelfParticipationPresenter,
        token: String,
        tid: String
    ) {
        NetworkClientRx.INSTANCE.api?.getSPDetailData(token, tid)
            ?.compose(NetworkClientRx.INSTANCE.IoMain())
            ?.subscribe(object : Subject<SPDetailModel>() {
                override fun hasObservers(): Boolean {
                    return false
                }

                override fun hasThrowable(): Boolean {
                    return false
                }

                override fun hasComplete(): Boolean {
                    return false
                }

                override fun getThrowable(): Throwable? {
                    return null
                }

                override fun subscribeActual(observer: Observer<in SPDetailModel>) {

                }

                override fun onSubscribe(d: Disposable) {

                }

                override fun onNext(ticketListBean: SPDetailModel) {
                    listener.onSPDetailSuccess(ticketListBean)
                }

                override fun onError(e: Throwable) {
                    listener.onFailure(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onComplete() {

                }
            })
    }


    override fun submitCCForS1Remarks(
        listener: CustomerCareFactory.CCQueryDetailforS1Presenter,
        model: java.util.HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).customerQuery(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ParticipationPlannedResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure("Failure")
                }

                override fun onNext(homeBean: ParticipationPlannedResponse) {
                    listener.onUpdateBdoSuccess(homeBean.message)

                }
            })
    }

    override fun getCCQueries(listener: CustomerCareFactory.CCPresenter, model: String) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getCustomerQueries(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CCQueriesModel?>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onFailure("Failure")
                }

                override fun onNext(homeBean: CCQueriesModel?) {

                    if (homeBean?.statusCode == 401) {
                        listener.onFailure(homeBean.message.toString())
                    } else if (homeBean?.statusCode == 409) {
                        EventBus.getDefault().post("logout")
                    } else if (homeBean?.statusCode != 409) {
                        listener.onQueriesSuccess(homeBean ?: CCQueriesModel())
                    } else {
                        if (homeBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        } else {
                            listener.onFailure(homeBean.message.toString())
                        }
                    }
                }
            })
    }

    override fun getCCQueriesforS1(listener: CustomerCareFactory.CCSPresenter, model: String) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getCustomerQueriesForS1(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CCQueriesForS1Model?>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure("Failure")
                }

                override fun onNext(homeBean: CCQueriesForS1Model?) {
                    if (homeBean?.statusCode != 409) {
                        listener.onQueriesSuccess(homeBean ?: CCQueriesForS1Model())
                    } else {
                        if (homeBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        } else {
                            listener.onFailure(homeBean.message.toString())
                        }
                    }

                }
            })
    }

    override fun submitCCRemarks(
        listener: CustomerCareFactory.CCQueryDetailPresenter,
        model: java.util.HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .customerQuerySubmit(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ParticipationPlannedResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure("Failure")
                }

                override fun onNext(homeBean: ParticipationPlannedResponse) {
                    listener.onUpdateRemarkSuccess(homeBean.message)

                }
            })
    }


    /**
     * Customer Care Queries
     */
    override fun submitActiveTicketReason(
        listener: ActivateTicketPresenterFactory.ReasonActivateTicketPresenter,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .activateTicketReason(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ParticipationPlannedResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.reasonResponse("Failure")
                }

                override fun onNext(homeBean: ParticipationPlannedResponse) {
                    listener.reasonResponse(homeBean.message)

                }
            })
    }


    override fun getActiveTicketPreviousData(
        listener: ActivateTicketPresenterFactory.ReasonActivateTicketPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getActiveTicketPlan(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PendingActiveTicketlistModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onActivateTicketResponseFailure("Failure")
                }

                override fun onNext(homeBean: PendingActiveTicketlistModel) {
                    if (homeBean.statusCode == 200) {
                        listener.onActivateTicketResponseSucess(homeBean)
                    } else {
                        listener.onActivateTicketResponseFailure(homeBean.message)
                    }
                }
            })
    }


    override fun getEfficiencyData(
        listener: ReportsFactory.ReportsPresenter,
        token: String,
        date: String
    ) {
        val map = HashMap<String, Any>()
        map.put("apiToken", token)
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getEfficiencyDate(map)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<EfficiencyResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onEfficiencyFailure("")
                }

                override fun onNext(obj: EfficiencyResponse) {
                    listener.onEfficiencySuccess(obj)
                }
            })
    }

    override fun getProductList(listener: ReportsFactory.ProductListPresenter, model: String) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getProductList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductListResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(listBean: ProductListResponse) {
                    if (listBean.statusCode == 200) {
                        listener.onSuccess(listBean)
                    } else {
                        if (listBean.statusCode == 409) {
                            EventBus.getDefault().post("logout")
                        } else {
                            listener.onFailure(listBean.message.toString())
                        }

                    }
                }
            })
    }

    override fun getBDOFilterList(listener: BDOFilterFactory.BDOFilterPresenter, model: String) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getBDOFilterList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<BDOFilterResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(editTicketBean: BDOFilterResponse) {
                    if (editTicketBean != null) {
                        when (editTicketBean.statusCode) {
                            200 -> {
                                listener.onSuccess(editTicketBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onFailure(editTicketBean.message)
                            }
                        }
                    }
                }
            })
    }

    override fun submitgetSelfParticipationData(
        listener: SelfParticipationFactory.SelfParticipationDetailPresenter,
        model: HashMap<String, RequestBody>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .submitSelfParticipationData(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ImageTicketResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailureParticipation(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(editTicketBean: ImageTicketResponse) {
                    listener.onSuccessUploadParticipation(editTicketBean)
                }
            })
    }

    override fun submitgetImageUploadData(
        listener: SelfParticipationFactory.SelfParticipationDetailPresenter,
        model: HashMap<String, RequestBody>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .uploadImageToTicketw(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ImageTicketResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(editTicketBean: ImageTicketResponse) {
                    listener.onSuccessUpload(editTicketBean)
                }
            })
    }

    override fun getReportsData(
        listener: ReportsFactory.ReportsPresenter,
        model: HashMap<String, Any>
    ) {

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getReportsData(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ReportsResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure("")
                }

                override fun onNext(obj: ReportsResponse) {
                    listener.onSuccessUpload(obj)
                }
            })
    }

    override fun submitTestimonialData(
        listener: TestimonialFactory.TestimonialPresenter,
        model: HashMap<String, RequestBody>,
        body: MultipartBody.Part
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .submitTestimonialData(model, body).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TestimonialResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                    println("SyncServiceInteractorImpl.onError : ${e.message}")
                }

                override fun onNext(editTicketBean: TestimonialResponse) {
                    listener.onSuccessUpload(editTicketBean)
                }
            })
    }

    override fun getSelfParticipationData(
        listener: SelfParticipationFactory.SelfParticipationPresenter,
        model: HashMap<String, Any>
    ) {

        NetworkClientRx.INSTANCE.api?.getSelfparticipationData(
            model.get("apiToken").toString(),
            model.get("manager_id").toString(),
            model.get("manager_level").toString()
        )?.compose(NetworkClientRx.INSTANCE.IoMain())
            ?.subscribe(object : Subject<SelfParticipationResponse>() {
                override fun hasObservers(): Boolean {
                    return false
                }

                override fun hasThrowable(): Boolean {
                    return false
                }

                override fun hasComplete(): Boolean {
                    return false
                }

                override fun getThrowable(): Throwable? {
                    return null
                }

                override fun subscribeActual(observer: Observer<in SelfParticipationResponse>) {

                }

                override fun onSubscribe(d: Disposable) {

                }

                override fun onNext(ticketListBean: SelfParticipationResponse) {
                    if (ticketListBean != null) {
                        when (ticketListBean.statusCode) {
                            200 -> {
                                listener.onRequestDataSuccess(ticketListBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onFailure(
                                    ticketListBean.message
                                        ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                                )
                            }
                        }
                    }

                }

                override fun onError(e: Throwable) {
                    listener.onFailure(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onComplete() {

                }
            })
    }

    override fun getSelfParticipationPreviousData(
        listener: SelfParticipationFactory.SPReasonPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getSelfParticipationPrevious(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PreviousPlanResponse?>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onPreviousPlanFailure("Failure")
                }

                override fun onNext(homeBean: PreviousPlanResponse?) {
                    if (homeBean?.statusCode == 200) {
                        listener.onPreviousPlanSuccess(homeBean)
                    } else {
                        listener.onPreviousPlanFailure(homeBean?.message.toString())
                    }
                }
            })
    }

    override fun planSelfParticipation(
        listener: SelfParticipationFactory.SelfParticipationPresenter,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .selfParticipationPlan(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ParticipationPlannedResponse?>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onPlanParticipationFailure("Failure")
                }

                override fun onNext(homeBean: ParticipationPlannedResponse?) {
                    if (homeBean?.statusCode == 200) {
                        listener.onPlanParticipationSuccess(homeBean)
                    } else {
                        listener.onPlanParticipationFailure(homeBean?.message.toString())
                    }
                }
            })
    }

    override fun submitSelfParticipationReason(
        listener: SelfParticipationFactory.SPReasonPresenter,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .selfParticipationReason(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ParticipationPlannedResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.reasonResponse("Failure")
                }

                override fun onNext(homeBean: ParticipationPlannedResponse) {
                    listener.reasonResponse(homeBean.message)

                }
            })
    }

    override fun getSelfParticipationHistoryData(
        listener: SelfParticipationFactory.SelfParticipationPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getSelfparticipationHistoryData(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SelfParticipationResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onFailure("Failure")
                }

                override fun onNext(homeBean: SelfParticipationResponse) {
                    if (homeBean != null) {
                        when (homeBean.statusCode) {
                            200 -> {
                                listener.onRequestDataSuccess(homeBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onFailure("Failure")
                            }
                        }

                    }
                }
            })
    }

    override fun activateTicket(
        listener: ActivateTicketPresenterFactory.TicketDetailPresenter,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).activateTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SubmitTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onActivateTicketSubmitResponseFailure("Failure")
                }

                override fun onNext(homeBean: SubmitTicketBean) {
                    listener.onActivateTicketSubmitResponseSucess(homeBean)
                }
            })
    }


    override fun getActiveTicketList(
        listener: ActivateTicketPresenterFactory.ActivateTicketPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).activeTickets(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ActiveTicketListModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onActivateTicketResponseFailure("Failure")
                }

                override fun onNext(homeBean: ActiveTicketListModel) {
                    when (homeBean.statusCode) {
                        409 -> {
                            EventBus.getDefault().post("logout")
                        }
                        200 -> {
                            listener.onActivateTicketResponseSucess(homeBean)
                        }
                        else -> {
                            listener.onActivateTicketResponseFailure("Failure")
                        }
                    }
                }
            })
    }

    override fun savePlanAtp(listener: ATPPresenter, model: PlanATPApiModel) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).planAtpSubmit(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PlanAtpSubmitResponseModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure("Failure")
                }

                override fun onNext(homeBean: PlanAtpSubmitResponseModel?) {
                    if (homeBean?.data == null) {
                        listener.onRequestDataResultFailure(homeBean?.message.toString())
                    } else {
                        listener.onSuccess(homeBean)

                    }
                }
            })
    }

    override fun getPLanBDOListOfS1(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getBDOListOfS1(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<BDOPlanListDBModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: BDOPlanListDBModel) {
                    if (homeBean != null) {
                        when (homeBean.statusCode) {
                            200 -> {
                                listener.getPLanBDOListOfS1Success(homeBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onRequestDataResultFailure()
                            }
                        }
                    }
                }
            })
    }

    override fun getPLanBDODetailOfS1(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String,
        id: Int
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getBDODetailOfBDOofS1(model, id).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<BDOPlanListDetailDBModel?>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.getPLanBDODetailOfS1Failure(e.message.toString())
                }

                override fun onNext(homeBean: BDOPlanListDetailDBModel?) {
                    if (homeBean?.statusCode == 401) {
                        listener.getPLanBDODetailOfS1Failure(homeBean?.message.toString())
                    } else {
                        listener.getPLanBDODetailOfS1Success(
                            homeBean ?: BDOPlanListDetailDBModel(),
                            id
                        )
                    }
                }
            })
    }


    override fun getPlanRequirement(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getATPPlanRequirement(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PlanRequirementDataModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: PlanRequirementDataModel) {
                    listener.onPlanRequirementSuccess(homeBean)
                }
            })
    }

    override fun savePlanActivityData(
        listener: SyncServiceInterfaceFactory.SyllabusHistoryPresenter,
        model: ProductSyllabusModel
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java)
            .cPlanActivity(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PlanSyllabusModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(SubmitFarmersBean: PlanSyllabusModel) {
                    listener.onPlanSyllabusSuccess(SubmitFarmersBean)
                }
            })
    }

    override fun getGetPlanActivityData(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String,
        time: String
    ) {

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .planActivityData(model, time)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PlanActivityDataModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: PlanActivityDataModel) {
                    listener.onPlanActivityDataResultSuccess(homeBean)
                }
            })
    }

    override fun getMySyllabusData(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String,
        value: Int
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).mySyllabus(model, value)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MySyllabusModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: MySyllabusModel) {
                    if (homeBean != null) {
                        when (homeBean.statusCode) {
                            200 -> {
                                listener.onMySyllabusDataResultSuccess(homeBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onRequestDataResultFailure()
                            }
                        }
                    }

                }
            })
    }

    override fun getMenuDataFromApi(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userMenu(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<HomeBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: HomeBean) {
                    listener.onRequestDataResultSuccess(homeBean)
                }
            })
    }

    override fun getMenuDataFromApi(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String,
        fbToken: String,
        isMenu: Boolean
    ) {
        var subscription = RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .userUMenu(model, fbToken).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<HomeBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: HomeBean) {
                    listener.onRequestDataResultSuccess(homeBean)
                }
            })
        if (isMenu) {
            Handler().postDelayed({
                if (subscription != null && !subscription.isUnsubscribed) {
                    subscription.unsubscribe()
                    subscription = null
                    listener.onRequestDataResultFailure()
                }
            }, 5000)
        }
    }

    override fun getProdCropList(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).prodCrop(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductCropResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: ProductCropResponse) {
                    listener.getProdCropSuccess(homeBean)
                }
            })
    }

    override fun getSharedSyllabusData(
        listener: SyncServiceInterfaceFactory.SyncDataManagerPresenter,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getSyllabusHistoryData(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SharedSyllabusHistoryModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRequestDataResultFailure()
                }

                override fun onNext(homeBean: SharedSyllabusHistoryModel) {
                    listener.getSharedSyllabusSuccess(homeBean)
                }
            })
    }
}


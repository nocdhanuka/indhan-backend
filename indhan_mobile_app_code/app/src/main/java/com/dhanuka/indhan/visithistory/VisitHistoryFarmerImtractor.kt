package com.dhanuka.indhan.visithistory

import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.validatefarmers.SuccessBean
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class VisitHistoryFarmerImtractor : VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor {
    override fun onCallFarmer(
        listener: VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor.OnFinishedListener,
        mParam: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).updateCallStatus(mParam)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onVisitListFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: SuccessBean) {
                    listener.onCallSuccess(villageBean)
                }
            }
            )
    }

    override fun onMakeLead(
        listener: VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor.OnFinishedListener,
        mLead: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).updateLeadStatus(mLead)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onVisitListFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: SuccessBean) {
                    listener.onStatusChangeSuccess(villageBean)
                }
            }
            )
    }


    override fun onGetVisitList(
        listener: VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getMarkVisit(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<VisitHistoryFarmerBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onVisitListFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: VisitHistoryFarmerBean) {

                    if (villageBean != null) {
                        when (villageBean.statusCode) {
                            200 -> {
                                listener.onVistListSuccess(villageBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onVisitListFailed(villageBean.message)
                            }
                        }
                    }
                }
            })
    }
}
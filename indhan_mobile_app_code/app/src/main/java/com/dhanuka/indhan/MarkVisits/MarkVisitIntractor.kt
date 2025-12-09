package com.dhanuka.indhan.MarkVisits

import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.farmerlist.FarmerBean
import okhttp3.MultipartBody
import okhttp3.RequestBody
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class MarkVisitIntractor : MarkVisitContractor.MarkVisitInteractor {
    override fun framerList(
        listener: MarkVisitContractor.MarkVisitInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getFarmerList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<FarmerBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onMarkVisitFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: FarmerBean) {
                    listener.onFramerList(villageBean)
                }
            }
            )
    }

    override fun villageList(
        listener: MarkVisitContractor.MarkVisitInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).villageList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MyVillageBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onMarkVisitFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: MyVillageBean) {
                    if (villageBean != null) {
                        when (villageBean.statusCode) {
                            200 -> {
                                listener.onVillageList(villageBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onMarkVisitFailed(
                                    villageBean.message
                                )
                            }
                        }
                    }

                }
            })
    }

    override fun markVisit(
        listener: MarkVisitContractor.MarkVisitInteractor.OnFinishedListener,
        model: HashMap<String, RequestBody>,
        body: MultipartBody.Part
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).markVisit(model, body)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MarkVisitBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onMarkVisitFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: MarkVisitBean) {
                    listener.onMarkVisitSuccess(villageBean)
                }
            }
            )
    }
}
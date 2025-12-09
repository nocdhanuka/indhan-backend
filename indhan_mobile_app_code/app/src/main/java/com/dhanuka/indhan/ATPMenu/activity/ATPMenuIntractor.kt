package com.dhanuka.indhan.ATPMenu.activity

import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class ATPMenuIntractor : ATPMenuContractor.ATPMenuInteractor {
    override fun onClosedATP(
        listener: ATPMenuContractor.ATPMenuInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).closeAtp(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ClosedATPBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onATPMenuFailed("")
                }

                override fun onNext(loginBean: ClosedATPBean) {
                    if (loginBean != null) {
                        when (loginBean.statusCode) {
                            200 -> {
                                listener.onCloseATPSuccess(loginBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onATPMenuFailed(loginBean.message)
                            }
                        }
                    }
                }
            }
            )
    }

    override fun onATPMenu(
        listener: ATPMenuContractor.ATPMenuInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).atpList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ATPBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onATPMenuFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(loginBean: ATPBean) {
                    if (loginBean != null) {
                        when (loginBean.status_code) {
                            200 -> {
                                listener.onATPMenuSuccess(loginBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onATPMenuFailed(
                                    loginBean.message
                                        ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                                )
                            }
                        }
                    }

                }
            }
            )
    }

}
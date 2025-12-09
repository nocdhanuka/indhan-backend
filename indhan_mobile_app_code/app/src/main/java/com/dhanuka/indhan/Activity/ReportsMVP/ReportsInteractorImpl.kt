package com.dhanuka.indhan.Activity.ReportsMVP

import com.dhanuka.indhan.Activity.ReportsMVP.Model.ReportsBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient

import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

/**
 * Created by Shivam on 2/28/2018.
 */

class ReportsInteractorImpl : ReportsContracts.ReportsInteractor {

    override fun userReports(
        listener: ReportsContracts.ReportsInteractor.OnFinishedListener,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .userReport(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ReportsBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onReportsFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(reportsBean: ReportsBean) {
                    listener.onReportsSucess(reportsBean)
                }
            })
    }
}

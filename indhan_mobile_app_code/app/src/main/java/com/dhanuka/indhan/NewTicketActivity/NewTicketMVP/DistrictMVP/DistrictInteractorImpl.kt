package com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel
import com.dhanuka.indhan.BuildConfig
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

class DistrictInteractorImpl : DistrictContracts.DistrictInteractor {

    override fun userDistrict(
        listener: DistrictContracts.DistrictInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        model["app_version"] = BuildConfig.VERSION_NAME
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userDistrict(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DistrictModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onDistrictFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(districtBean: DistrictModel) {
                    listener.onDistrictSucess(districtBean)
                }
            })
    }
}

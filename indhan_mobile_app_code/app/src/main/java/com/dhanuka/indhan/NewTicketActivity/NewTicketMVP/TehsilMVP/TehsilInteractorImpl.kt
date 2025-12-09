package com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilModel
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

class TehsilInteractorImpl : TehsilContracts.TehsilInteractor {

    override fun userTehsil(
        listener: TehsilContracts.TehsilInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userTehsil(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TehsilModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onTehsilFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(tehsilBean: TehsilModel) {
                    listener.onTehsilSucess(tehsilBean)
                }
            })
    }
}

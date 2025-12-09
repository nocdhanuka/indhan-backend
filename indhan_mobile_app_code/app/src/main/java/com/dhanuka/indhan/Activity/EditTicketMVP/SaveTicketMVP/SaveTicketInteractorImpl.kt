package com.dhanuka.indhan.Activity.EditTicketMVP.SaveTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

class SaveTicketInteractorImpl : SaveTicketContracts.SaveTicketInteractor {

    override fun userSaveTicket(
        listener: SaveTicketContracts.SaveTicketInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).updatedTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SubmitTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onSaveTicketFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(saveTicketBean: SubmitTicketBean) {
                    listener.onSaveTicketSucess(saveTicketBean)
                }
            })
    }
}

package com.dhanuka.indhan.Activity.EditTicketMVP

import android.os.Handler
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.EditTicketBean
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import okhttp3.RequestBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

class EditTicketInteractorImpl : EditTicketContracts.EditTicketInteractor {
    override fun villageList(
        listener: EditTicketContracts.EditTicketInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).allVillageList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MyVillageBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onEditTicketFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: MyVillageBean) {
                    listener.onVillageList(villageBean)
                }
            }
            )
    }

    override fun uploadPhoto(
        listener: EditTicketContracts.EditTicketPresenter,
        image: RequestBody,
        tid: RequestBody,
        token: RequestBody,
        fileNAme: String,
        latitude: RequestBody,
        longitude: RequestBody,
        date: RequestBody
    ) {
        val map: HashMap<String, RequestBody> = HashMap()
        map["photo\"; filename=\"$fileNAme\" "] = image
        map["api_token"] = token
        map["tId"] = tid
        map["latitude"] = latitude
        map["longitude"] = longitude
        map["updated_at"] = date

        val client = RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
        val call: Call<ImageTicketResponse> = client.uploadImageToTicket(map)

        call.enqueue(object : Callback<ImageTicketResponse?> {
            override fun onResponse(
                call: Call<ImageTicketResponse?>,
                response: Response<ImageTicketResponse?>
            ) {
                if (call.isCanceled) {
                    listener.onFailure("Slow Internet Connection. Your Data is Saved Locally.")
                } else {
                    if (response.body() != null) {
                        listener.onSuccessUpload(response.body()!!)
                    } else {
                        listener.onFailure("Server error. Please try again after sometime or with moderate internet connection.")
                    }
                }
            }

            override fun onFailure(call: Call<ImageTicketResponse?>, t: Throwable) {
                if (call.isCanceled) {
                    listener.onFailure("Slow Internet Connection. Your Data is Saved Locally.")
                } else {
                    listener.onFailure(
                        t.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }
            }
        })
        Handler().postDelayed({
            call.cancel()
        }, 5000)
    }


    override fun userEditTicket(
        listener: EditTicketContracts.EditTicketInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).editTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<EditTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onEditTicketFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(editTicketBean: EditTicketBean) {
                    listener.onEditTicketSucess(editTicketBean)
                }
            })
    }
}

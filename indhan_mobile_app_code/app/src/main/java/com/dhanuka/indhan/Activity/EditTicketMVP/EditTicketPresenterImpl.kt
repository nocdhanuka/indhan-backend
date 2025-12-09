package com.dhanuka.indhan.Activity.EditTicketMVP

import com.dhanuka.indhan.Activity.EditTicketMVP.Model.EditTicketBean
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse
import com.dhanuka.indhan.MyVillage.MyVillageBean
import okhttp3.RequestBody
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

class EditTicketPresenterImpl(private val mIEditTicketView: EditTicketContracts.IEditTicketView?) :
    EditTicketContracts.EditTicketPresenter,
    EditTicketContracts.EditTicketInteractor.OnFinishedListener {
    override fun onVillageList(visit: HashMap<String, Any>) {
        mEditTicketInteractor.villageList(this, visit)

    }

    override fun onVillageList(items: MyVillageBean) {
        mIEditTicketView?.setVillageList(items)
    }

    override fun onFailure(response: String) {
        mIEditTicketView?.hideProgress()
        mIEditTicketView?.onFailure(response)
    }

    override fun onSubmitPhoto(
        image: RequestBody,
        tid: RequestBody,
        token: RequestBody,
        fileNAme: String,
        latitude: RequestBody,
        longitude: RequestBody,
        date: RequestBody
    ) {
        mIEditTicketView?.showProgress()
        mEditTicketInteractor.uploadPhoto(
            this,
            image,
            tid,
            token,
            fileNAme,
            latitude,
            longitude,
            date
        )
    }


    override fun onSuccessUpload(response: ImageTicketResponse) {
        mIEditTicketView?.hideProgress()
        if (response.statusCode == 200)
            mIEditTicketView?.setItems(response.data.url)
        else {
            mIEditTicketView?.onFailure(response.message)
        }
    }

    private val mEditTicketInteractor: EditTicketContracts.EditTicketInteractor

    init {
        this.mEditTicketInteractor = EditTicketInteractorImpl()
    }


    override fun onSubmitClick(mEditTicketRequest: HashMap<String, Any>) {
        mIEditTicketView?.showProgress()
        mEditTicketInteractor.userEditTicket(this, mEditTicketRequest)
    }


    override fun onEditTicketSucess(items: EditTicketBean) {
        if (mIEditTicketView != null) {
            mIEditTicketView.setItems(items)
            mIEditTicketView.hideProgress()
        }
    }

    override fun onEditTicketFailed(errorMessage: String) {
        mIEditTicketView?.hideProgress()
        mIEditTicketView?.onFailure(errorMessage)
    }
}

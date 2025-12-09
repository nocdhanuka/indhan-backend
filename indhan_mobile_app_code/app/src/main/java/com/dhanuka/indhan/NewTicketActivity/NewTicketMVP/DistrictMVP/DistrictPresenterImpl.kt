package com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

class DistrictPresenterImpl(private val mIDistrictView: DistrictContracts.IDistrictView?) :
    DistrictContracts.DistrictPresenter, DistrictContracts.DistrictInteractor.OnFinishedListener {
    private val mDistrictInteractor: DistrictContracts.DistrictInteractor

    init {
        this.mDistrictInteractor = DistrictInteractorImpl()
    }


    override fun onSubmitClick(mDistrictRequest: HashMap<String, Any>) {
        mIDistrictView!!.showProgress()
        mDistrictInteractor.userDistrict(this, mDistrictRequest)
    }

    override fun onDistrictSucess(items: DistrictModel) {
        if (mIDistrictView != null) {
            mIDistrictView.setItems(items)
            mIDistrictView.hideProgress()
        }
    }

    override fun onDistrictFailed(errorMessage: String) {
        if (mIDistrictView != null) {
            mIDistrictView.hideProgress()
        }
    }
}

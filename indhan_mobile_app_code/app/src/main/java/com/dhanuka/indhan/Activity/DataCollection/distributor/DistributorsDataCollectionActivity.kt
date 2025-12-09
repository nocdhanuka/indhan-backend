package com.dhanuka.indhan.Activity.DataCollection.distributor

import android.content.Intent
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.Constants
import com.dhanuka.indhan.Model.DistributorResponse
import com.dhanuka.indhan.Model.MarketResponse
import com.dhanuka.indhan.Model.TerritoryResponse
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.ColorModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinnerSelectable
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_distributors_data_collection.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class DistributorsDataCollectionActivity : BaseActivity(), View.OnClickListener {
    private var territory = java.util.ArrayList<String>()
    private var distributor = java.util.ArrayList<ColorModel>()
    private var market = java.util.ArrayList<String>()
    private var districtId: String = " "
    private lateinit var preference: Preference

    private var selectTerritory: NumberSpinner? = null
    private var selectMarket: NumberSpinner? = null
    private var selectDistributor: NumberSpinnerSelectable? = null

    private lateinit var districtStr: String
    private lateinit var territoryStr: String
    private lateinit var marketStr: String
    private lateinit var distributorStr: String
    private lateinit var stateId: String

    private var territoryList = ArrayList<TerritoryResponse.TerritoryData>()
    private var marketList = ArrayList<MarketResponse.Data>()
    private var distributorList = ArrayList<DistributorResponse.Datum>()

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtSubmitDistributor -> {
                if (txtSubmitDistributor.text == "Continue") {
                    val bundle = Bundle()
                    val distObject =
                        distributorList.find { it.distributerforname == distributorStr }!!
                    val marketObject = marketList.find { it.market == marketStr }!!
                    bundle.putString("district_id", marketObject.district_id.toString())
                    bundle.putString("district_name", districtStr)
                    bundle.putString("territory_id", marketObject.territory_id.toString())
                    bundle.putString("state_id", stateId)
                    bundle.putString("territory_name", territoryStr)
                    bundle.putString("market_id", marketObject.market_id.toString())
                    bundle.putString("market_name", marketStr)
                    bundle.putString("distributor_data", Gson().toJson(distObject))
                    val intent = Intent(activity, AddNewDistributor::class.java)
                    intent.putExtras(bundle)
                    startActivityForResult(intent, Constants.REQUEST_ADD_DISTRIBUTOR)
                } else {
                    val bundle = Bundle()
                    val marketObject = marketList.find { it.market == marketStr }!!
                    bundle.putString("district_id", marketObject.district_id.toString())
                    bundle.putString("district_name", districtStr)
                    bundle.putString("territory_id", marketObject.territory_id.toString())
                    bundle.putString("state_id", stateId)
                    bundle.putString("territory_name", territoryStr)
                    bundle.putString("market_id", marketObject.market_id.toString())
                    bundle.putString("market_name", marketStr)
                    val intent = Intent(activity, AddNewDistributor::class.java)
                    intent.putExtras(bundle)
                    startActivityForResult(intent, Constants.REQUEST_ADD_DISTRIBUTOR)
                }
            }
            R.id.txtSubmit -> {
                saveMarketData()
            }
            R.id.txtSelectTerritory -> {
                if (territoryList.size > 0) {
                    selectTerritory?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }

                } else {
                    makeToast("No territory found in district.")
                }
            }
            R.id.txtMarketName -> {
                if (marketList.size > 0) {
                    selectMarket?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }

                } else {
                    makeToast("No market found please add new market.")
                }
            }
            R.id.txtDistributorName -> {
                if (distributorList.size > 0) {
                    selectDistributor?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }
                } else {
                    makeToast("No channels found please add new channel in market.")
                }
            }
        }
    }

    private fun saveMarketData() {
        if (!TextUtils.isEmpty(txtAddMarket.text.toString().trim())) {
            showProgress()
            val territoryObject: TerritoryResponse.TerritoryData =
                territoryList.find { it.territory == territoryStr }!!

            val query = HashMap<String, Any>()
            query["apiToken"] = preference.getString("token")
            query["state_id"] = stateId
            query["district_id"] = territoryObject.district_id.toString()
            query["territory_id"] = territoryObject.territory_id.toString()
            query["market"] = txtAddMarket.text.toString().trim()

            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .saveMarketData(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<MarketResponse>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        hideProgress()
                    }

                    override fun onNext(listBean: MarketResponse) {
                        hideProgress()
                        if (listBean.status_code == 200) {
                            marketList.addAll(listBean.data)
                            marketStr = txtAddMarket.text.toString()
                            txtMarketName.text = marketStr
                            val marketObject: MarketResponse.Data =
                                marketList.find { it.market == marketStr }!!
                            val bundle = Bundle()
                            bundle.putString("district_id", marketObject.district_id.toString())
                            bundle.putString("state_id", stateId)
                            bundle.putString("district_name", districtStr)
                            bundle.putString("territory_id", marketObject.territory_id.toString())
                            bundle.putString("territory_name", territoryStr)
                            bundle.putString("market_id", marketObject.market_id.toString())
                            bundle.putString("market_name", marketStr)
                            txtAddMarket.setText("")
                            val intent = Intent(activity, AddNewDistributor::class.java)
                            intent.putExtras(bundle)
                            startActivityForResult(intent, Constants.REQUEST_ADD_DISTRIBUTOR)
                        }
                    }
                })
        } else {
            makeToast("Please enter market name, it cannot be blank.")
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_distributors_data_collection)
        preference = Preference(this)
        val name = preference.getString("Username")
        txtName.text = "FL - $name"
        getIntentData()
        setListeners()
    }

    private fun getIntentData() {

        intent?.getStringExtra("district_name").let {
            districtStr = it.toString()
        }

        intent?.getStringExtra("state_id").let {
            stateId = it.toString()
        }
        intent?.getStringExtra("district_id").let {
            districtId = it.toString()
            getTerritory(districtId.toInt())
        }
    }

    private fun getTerritory(districtId: Int) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["district_id"] = districtId
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getTerritory(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TerritoryResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: TerritoryResponse) {
                    hideProgress()
                    territoryList.clear()
                    territory.clear()
                    if (listBean.status_code == 200) {
                        territoryList.addAll(listBean.data)
                        setTerritoryData()
                    }
                }
            })
    }

    private fun setTerritoryData() {
        territoryList.forEach {
            territory.add(it.territory)
        }
        selectTerritory =
            NumberSpinner(this, territory, "Search or Select Territory")
        selectTerritory?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtMarketName.text = ""
                txtDistributorName.text = ""
                txtMarketName.visibility = View.VISIBLE
                txtAddMarket.setText("")
                txtAddMarket.visibility = View.VISIBLE
                txtOr.visibility = View.VISIBLE
                txtSubmit.visibility = View.VISIBLE
                txtDistributorName.visibility = View.GONE
                txtSubmitDistributor.visibility = View.GONE
                txtOrDistributor.visibility = View.GONE
                distributor.clear()
                market.clear()
                territoryStr = item
                txtSelectTerritory.text = item
                val territoryObject: TerritoryResponse.TerritoryData =
                    territoryList.find { it.territory == territoryStr }!!
                getMarketData(territoryObject.territory_id)
            }
        })
    }

    private fun getMarketData(territoryId: Int) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["territory_id"] = territoryId
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getMarketList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MarketResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: MarketResponse) {
                    hideProgress()
                    marketList.clear()
                    market.clear()
                    if (listBean.status_code == 200) {
                        marketList.addAll(listBean.data)
                        setMarketData("1")
                    }
                }
            })
    }

    private fun setMarketData(type: String) {
        market.clear()
        if (marketList.size > 0) {
            marketList.forEach {
                market.add(it.market)
            }
            selectMarket =
                NumberSpinner(this, market, "Search or Select Market")
            selectMarket?.bindOnSpinerListener(object : OnSpinerItemClick {
                override fun onClick(item: String, position: Int) {
                    txtAddMarket.visibility = View.GONE
                    txtOr.visibility = View.GONE
                    txtSubmit.visibility = View.GONE
                    txtDistributorName.text = ""
                    txtDistributorName.visibility = View.VISIBLE
                    txtSubmitDistributor.visibility = View.VISIBLE
                    txtOrDistributor.visibility = View.VISIBLE
                    txtSubmitDistributor.text = "Add New Channel"
                    marketStr = item
                    txtMarketName.text = item
                    distributor.clear()
                    val marketObject: MarketResponse.Data =
                        marketList.find { it.market == marketStr }!!
                    getDistributorData(marketObject)
                }
            })
            if (type == "0") {
                txtAddMarket.visibility = View.GONE
                txtOr.visibility = View.GONE
                txtSubmit.visibility = View.GONE
                txtDistributorName.visibility = View.VISIBLE
                txtSubmitDistributor.visibility = View.VISIBLE
                txtOrDistributor.visibility = View.VISIBLE
                marketStr = txtAddMarket.text.toString()
                txtMarketName.text = marketStr
                val marketObject: MarketResponse.Data =
                    marketList.find { it.market == marketStr }!!
                getDistributorData(marketObject)
            }
        } else {
            makeToast("No market found please add new market.")
        }
    }

    private fun getDistributorData(marketObject: MarketResponse.Data) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["territory_id"] = marketObject.territory_id
        query["district_id"] = marketObject.district_id
        query["market"] = marketObject.market_id

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getDistributor(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DistributorResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: DistributorResponse) {
                    hideProgress()
                    distributorList.clear()
                    distributor.clear()
                    if (listBean.status_code == 200) {
                        distributorList.addAll(listBean.data)
                        setDistributorData()
                    }
                }
            })
    }

    private fun setDistributorData() {
        distributorList.sortBy { it.connected_retailers }
        distributorList.forEach {
            val colorModel = ColorModel()
            if (TextUtils.isEmpty(it.ratings)
            ) {
                colorModel.color = "red"
            } else {
                if (it.ratings == "0") {
                    colorModel.color = "red"
                } else {
                    colorModel.color = "green"
                }
            }
            colorModel.name = it.distributerforname
            distributor.add(colorModel)
        }

        selectDistributor =
            NumberSpinnerSelectable(this, distributor, "Search or Select Channel")
        selectDistributor?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtSubmitDistributor.visibility = View.GONE
                txtOrDistributor.visibility = View.GONE
                txtSubmitDistributor.visibility = View.VISIBLE
                txtSubmitDistributor.text = "Continue"
                distributorStr = item
                txtDistributorName.text = item
            }
        })
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == Constants.REQUEST_ADD_DISTRIBUTOR) {
                setResult(RESULT_OK)
                finish()
            }
        }
    }

    private fun setListeners() {
        txtSelectTerritory.setOnClickListener(this)
        txtMarketName.setOnClickListener(this)
        txtDistributorName.setOnClickListener(this)
        txtSubmitDistributor.setOnClickListener(this)
        txtSubmit.setOnClickListener(this)
    }

}
package com.dhanuka.indhan.PlanATP.View

import android.app.Activity
import android.app.Dialog
import android.content.DialogInterface
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.os.Handler
import android.view.View
import android.view.Window
import android.widget.CompoundButton
import android.widget.RadioGroup
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.DataSourceSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictContracts
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictPresenterImpl
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictBean
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.StateBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.StateSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilBean
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilModel
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilContracts
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilPresenterImpl
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.TehsilSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.DbStructure.MyContentObserver
import com.dhanuka.indhan.DataBase.TableDataModel.*
import com.dhanuka.indhan.DataBase.TableStructureModels.CropTable
import com.dhanuka.indhan.DataBase.TableStructureModels.DataSourceTable
import com.dhanuka.indhan.DataBase.TableStructureModels.ProductTable
import com.dhanuka.indhan.DataBase.TableStructureModels.StateTable
import com.dhanuka.indhan.DataSyncServices.DataSyncManager
import com.dhanuka.indhan.DataSyncServices.Model.PlanAtpSubmitResponseModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.Model.LanguageModel
import com.dhanuka.indhan.Model.PAtpApiModel
import com.dhanuka.indhan.Model.PlanATPApiModel
import com.dhanuka.indhan.Model.TerritoryResponse
import com.dhanuka.indhan.PlanATP.Model.DataSourceListBean
import com.dhanuka.indhan.PlanATP.Model.PresenterDataResponse
import com.dhanuka.indhan.PlanATP.Model.VillageListTehsil
import com.dhanuka.indhan.PlanATP.TehsilVillageSpinnerDialog
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.PlanAtpListAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.LanguageSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.*
import com.google.firebase.analytics.FirebaseAnalytics
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_plan_atp.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import noman.weekcalendar.listener.OnDateClickListener
import org.joda.time.DateTime
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by John on 5/15/2018.
 */
class PlanAtpActivity : BaseActivity(), SyncServiceInterfaceFactory.FailureListener,
    MyContentObserver.DbValueChangeListener, DistrictContracts.IDistrictView,
    TehsilContracts.ITehsilView, OnDateClickListener,
    PlanAtpListAdapter.BDODescriptorClickListener {
    private var presenterObject: PresenterDataResponse.Data? = null
    private var monitorObject: PresenterDataResponse.Data? = null
    private var isMonitorEnable = false
    private var product_id = 0
    private var product = ""
    private var date = ""
    private var crop_id = 0
    private var teritoryId: Int = 0
    private var crop = ""
    private var teritory = ""
    private var prod_one = ""
    private var prod_two = ""
    private var data_source_id = 0
    private var data_source = ""
    private var Token = ""
    private var ddName = ""
    private var ddID = ""
    private lateinit var preference: Preference
    private var state_id = 0
    private var state = ""
    private var district_id = 0
    private var district = ""
    private var tehsil_id = 0
    private var tehsil = ""
    private var expected_farmer = 0
    private var village = ""
    private var expected_pda = 0
    private var language_id = 0
    private var isFl = "0"
    private var selectTerritory: NumberSpinner? = null
    private var languageSpinner: LanguageSpinner? = null
    private var productSpinner: ProductSpinnerDialog? = null
    private var productSpinnerOne: ProductSpinnerDialog? = null
    private var productSpinnerTwo: ProductSpinnerDialog? = null
    private var districSpinner: DistrictSpinnerDialog? = null
    private var tehsilSpinner: TehsilSpinnerDialog? = null
    private var villageSpinner: TehsilVillageSpinnerDialog? = null
    private var cropSpinner: CropSpinnerDialog? = null
    private var presenterSpinner: NumberSpinner? = null
    private var monitorSpinner: NumberSpinner? = null
    private var stateSpinner: StateSpinnerDialog? = null
    private var dataSourceSpinner: DataSourceSpinnerDialog? = null
    private var presenterDataList: ArrayList<PresenterDataResponse.Data> = ArrayList()
    private var monitorDataList: ArrayList<PresenterDataResponse.Data> = ArrayList()
    private var presenterStringDataList: ArrayList<String> = ArrayList()
    private var monitorStringDataList: ArrayList<String> = ArrayList()

    private var territoryList = ArrayList<TerritoryResponse.TerritoryData>()
    private var territory = java.util.ArrayList<String>()

    private var languageList: ArrayList<LanguageModel> = ArrayList()
    private var dataSourceList: ArrayList<DataSourceBean> = ArrayList()
    private var stateModelLis: ArrayList<StateBean> = ArrayList()
    private var productList: ArrayList<ProductBean> = ArrayList()
    private var productList2: ArrayList<ProductBean> = ArrayList()
    private var productList3: ArrayList<ProductBean> = ArrayList()
    private var cropList: ArrayList<CropBean> = ArrayList()

    private var tehsilList: ArrayList<TehsilBean> = ArrayList()
    private var villageList: ArrayList<VillageListTehsil.Data> = ArrayList()
    private var districtList: ArrayList<DistrictBean> = ArrayList()

    private var planAtpList: ArrayList<PlanAtpDBModel> = ArrayList()
    var cropName: String = ""
    var month: String = ""
    lateinit var observerState: MyContentObserver
    lateinit var observerDatasources: MyContentObserver
    lateinit var observerProduct: MyContentObserver
    lateinit var observerCrop: MyContentObserver
    lateinit var adapter: PlanAtpListAdapter
    private lateinit var mdistrictPresenter: DistrictContracts.DistrictPresenter
    private lateinit var mtehsilPresenter: TehsilContracts.TehsilPresenter
    private var counter = 0
    var syll_model: MySyllabusDBModel? = null
    private var designation = ""
    private var isSLFL: Boolean = false
    lateinit var mActivity: Activity

    private lateinit var appDatabase: AppDatabase

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_plan_atp)
        mActivity = this
        preference = Preference(this)
        state_id = preference.getInt("state_id")
        state = preference.getString("state")
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        designation = preference.getString("designation")
        if (designation == "SALES-FL" || designation == "COUNTERPART-FL" || designation == "SL-FL") {
            txtQuestionAssign.visibility = View.GONE
            rgATP.visibility = View.GONE
            isFl = "1"
        }
        spinner_state.text = state
        date = DateTime().plusDays(1).toString()
        mdistrictPresenter = DistrictPresenterImpl(this)
        mtehsilPresenter = TehsilPresenterImpl(this)

        Token = preference.getString("token")
        initialiseAndBindSpinner()
        getDataSourceList(Token)
        initiateBackendcall()
        initRcView()
        upDateRcView()
        updateRemainingDaysTV()
        updateCalenderWithSelectedTime()
        val b = intent.extras
        if (b != null) {
            syll_model = b.getSerializable("model") as MySyllabusDBModel
            spinner_crop.text = syll_model?.crop_name
            spinner_Product.text = syll_model?.product_name
            product_id = syll_model?.product_id ?: 0
            product = syll_model?.product_name ?: ""
            crop_id = syll_model?.crop_id ?: 0
            filterOther(1)
        }
        updateListData()
    }

    val ListOFSelectedDated: ArrayList<DateTime> = ArrayList()

    private fun updateCalenderWithSelectedTime() {
        val test = DatabaseManager.getAllTimePlanList(this);
        ListOFSelectedDated.clear()
        test.forEach {
            val time = DateTime(it)
            ListOFSelectedDated.add(time)
        }
    }

    private fun updateRemainingDaysTV() {
        if (product_id != 0 && crop_id != 0) {
            val monthI = Utility.getMontfromDate(date)
            if (syll_model == null)
                syll_model = DatabaseManager.getAllMySyllabusListonThebasisofCropnProduct(
                    this,
                    product_id,
                    crop_id,
                    monthI
                )
            if (syll_model != null) {
                if (syll_model?.product_id != product_id && syll_model?.crop_id != crop_id) {
                    syll_model = null
                    updateRemainingDaysTV()
                }
                if (syll_model != null && syll_model?.no_of_days != null) {
                    if (syll_model?.no_of_days!! > 0) {
                        val list = DatabaseManager.getPlannedATPListOnBasisOfCropAndProd(
                            this,
                            syll_model!!.month,
                            crop_id,
                            product_id
                        )
                        var remainDays = 0

                        if (syll_model?.month == monthI) {
                            remainDays = syll_model?.no_of_days!! - list.size

                        } else {
                            remainDays = syll_model?.no_of_days!!
                        }
                        if (remainDays != null && remainDays >= 0) {
                            remainDays -= syll_model?.atp!!
                            if (remainDays < 0) remainDays = 0
                            layout_remaining_days.visibility = View.VISIBLE
                            remaing_days_tv.text =
                                "Remaining activities for " + spinner_Product.text + " and " + spinner_crop.text + " :"
                            num_days_tv.text = remainDays.toString()
                        } else {
                            layout_remaining_days.visibility = View.GONE
                        }
                    }
                }
            } else {
                layout_remaining_days.visibility = View.GONE

            }
        } else {
            layout_remaining_days.visibility = View.GONE
        }
    }

    private fun initRcView() {
        rvListPlanAtpList.layoutManager =
            LinearLayoutManager(this)
        adapter = PlanAtpListAdapter(planAtpList, this)
        rvListPlanAtpList.adapter = adapter
        adapter.setLongClickListener(this)
    }

    private fun initialiseAndBindSpinner() {
        calendar.setOnDateClickListener(this)
        tvReAddNewActivity.setOnClickListener(this)
        iCross.setOnClickListener(this)
        submit.setOnClickListener(this)
        spinner_Product.setOnClickListener(this)
        spinner_crop.setOnClickListener(this)
        spinner_data_source.setOnClickListener(this)
        spinner_territory.setOnClickListener(this)
        spinner_Product_one.setOnClickListener(this)
        spinner_Product_two.setOnClickListener(this)
        spinner_monitor_source.setOnClickListener(this)
        spinner_presenter_source.setOnClickListener(this)
        spinner_dd.setOnClickListener(this)
        etRetVillage.setOnClickListener(this)
        spinnerLanguage.setOnClickListener(this)
        //  spinner_state.setOnClickListener(this)
        spinner_district.setOnClickListener(this)
        updateCalenderWithSelectedTime()
        calendar.setStartDate(DateTime().plusDays(1), ListOFSelectedDated)
        /*    calendar.setSelectedDate(DateTime().plusDays(1))*/
        calendar.setSelectedDate(DateTime().plusDays(1))
        tvMonth.text = Utility.getDateCurrent(DateTime().plusDays(1))
        spinner_tehsil.setOnClickListener(this)
        stateSpinner = StateSpinnerDialog(this, stateModelLis, "Select State")
        languageSpinner = LanguageSpinner(this, languageList, "Select Language")
        dataSourceSpinner = DataSourceSpinnerDialog(this, dataSourceList, "Select Data Source")
        productSpinner = ProductSpinnerDialog(this, productList, "Select Product")
        selectTerritory =
            NumberSpinner(this, territory, "Search or Select Territory")
        productSpinnerOne = ProductSpinnerDialog(this, productList2, "Select Product Two")
        productSpinnerTwo = ProductSpinnerDialog(this, productList3, "Select Product Three")
        cropSpinner = CropSpinnerDialog(this, cropList, "Select Crop")
        districSpinner = DistrictSpinnerDialog(this, districtList, "Select District")
        tehsilSpinner = TehsilSpinnerDialog(this, tehsilList, "Select Tehsil")
        villageSpinner = TehsilVillageSpinnerDialog(this, villageList, "Select Village")

        languageSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerLanguage.text = languageList[position].language
                language_id = languageList[position].id.toInt()
            }
        })

        dataSourceSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                tvPresenterLabel.visibility = View.GONE
                spinner_presenter_source.visibility = View.GONE
                viewPresenter.visibility = View.GONE
                tvMonitorLabel.visibility = View.GONE
                spinner_monitor_source.visibility = View.GONE
                viewMonitor.visibility = View.GONE
                spinner_presenter_source.text = "Select Presenter"
                spinner_monitor_source.text = "Select Monitor"
                presenterObject = null
                monitorObject = null
                isMonitorEnable = false
                spinner_data_source.text = item
                if (item.equals("Village Meeting", true)) {
                    crop_two_layout.visibility = View.VISIBLE
                    spinner_Product_one.text = "Select Product Two"
                    spinner_Product_two.text = "Select Product Three"
                    prod_one = ""
                    prod_two = ""
                } else {
                    crop_two_layout.visibility = View.GONE
                    spinner_Product_one.text = "Select Product Two"
                    spinner_Product_two.text = "Select Product Three"
                    prod_one = ""
                    prod_two = ""
                }

                if (item.equals("E-Meeting", true)) {
                    tvPresenterLabel.visibility = View.VISIBLE
                    spinner_presenter_source.visibility = View.VISIBLE
                    viewPresenter.visibility = View.VISIBLE
                    getDataForPresenter()
                }
                if (item.equals("IFC", ignoreCase = true)) {
                    ddID = "0"
                    ddName = "My Self"
                    spinner_dd.text = ddName
                    spinner_dd.isEnabled = false
                } else {
                    spinner_dd.isEnabled = true
                }

                data_source = item
                data_source_id = dataSourceList[position].dsID!!
            }
        })



        cropSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_crop.text = item
                crop = item
                crop_id = cropList[position].getcID()!!
                syll_model = null
                updateRemainingDaysTV()
                spinner_Product.text = "Select product"
                spinner_Product_one.text = "Select Product Two"
                spinner_Product_two.text = "Select Product Three"
                updateProductBeanList(crop_id, 1)
            }
        })

        productSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_Product.text = item
                product = item
                product_id = productList.get(position).getpID()!!
                updateRemainingDaysTV()
//                updateCropBeanList(product_id)
                syll_model = null
                spinner_Product_one.text = "Select Product Two"
                spinner_Product_two.text = "Select Product Three"
                filterOther(1)
            }
        })

        productSpinnerOne?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_Product_one.text = item
                prod_one = productList2[position].getpID().toString()
                spinner_Product_two.text = "Select Product Three"
                filterOther(2)

            }
        })

        productSpinnerTwo?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_Product_two.text = item
                prod_two = productList3[position].getpID().toString()
                filterOther(3)
            }
        })

        stateSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                state = item
                state_id = stateModelLis[position].getsID()!!
                spinner_state.text = item
            }
        })

        districSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                district = item
                district_id = districtList.get(position).getdID()!!
                spinner_district.text = item
                if (district_id != 0) {
                    tehsil_id = 0
                    village = ""
                    etRetVillage.text = "Select village"
                    spinner_tehsil.text = "Select Tehsil"
                    val query = HashMap<String, Any>()
                    query.put("apiToken", Token)
                    query.put("dId", district_id)
                    getTerritoryListFromDb(district_id)
                    tehsilList.clear()
                    villageList.clear()
                    tehsilSpinner?.updatelist(tehsilList)
                    villageSpinner?.updatelist(villageList)
                    if (CheckConnectionStatus.isInternetAvailable(this@PlanAtpActivity)) {
                        mtehsilPresenter.onSubmitClick(query)
                    } else {
                        makeToast(resources.getString(R.string.no_internet_connection))
                    }
                }
            }
        })

        tehsilSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_tehsil.text = item
                tehsil = item
                tehsil_id = tehsilList.get(position).gettID()!!
                village = ""
                etRetVillage.text = "Select village"
                villageList.clear()
                villageSpinner?.updatelist(villageList)
                if (CheckConnectionStatus.isInternetAvailable(this@PlanAtpActivity)) {
                    getVillageList(tehsil_id.toString())
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }

            }
        })

        villageSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (villageList.size > 0) {
                    village = villageList[position].villageId
                    etRetVillage.text = villageList[position].village
                }
            }
        })


        rdMySelf.setOnCheckedChangeListener(object : RadioGroup.OnCheckedChangeListener,
            CompoundButton.OnCheckedChangeListener {
            override fun onCheckedChanged(buttonView: CompoundButton?, isChecked: Boolean) {
                if (isChecked) {
                    isFl = "1"
                }
            }

            override fun onCheckedChanged(group: RadioGroup?, checkedId: Int) {

            }
        })

        rdCP.setOnCheckedChangeListener(object : RadioGroup.OnCheckedChangeListener,
            CompoundButton.OnCheckedChangeListener {
            override fun onCheckedChanged(buttonView: CompoundButton?, isChecked: Boolean) {
                if (isChecked) {
                    isFl = "2"
                }
            }

            override fun onCheckedChanged(group: RadioGroup?, checkedId: Int) {

            }
        })

    }

    private fun getTerritoryListFromDb(districtId: Int) {
        spinner_territory.text = "Select Territory"
        territoryList.clear()
        territory.clear()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                territoryList = appDatabase.territoryDao()
                    .getAllTerritoryList(districtId) as ArrayList<TerritoryResponse.TerritoryData>
            }.run {
                withContext(Dispatchers.Main) {
                    setTerritoryData()
                }
            }
        }
    }


    private fun setTerritoryData() {
        territoryList.forEach {
            territory.add(it.territory)
        }
        selectTerritory =
            NumberSpinner(this, territory, "Search or Select Territory")
        selectTerritory?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinner_territory.text = item
                teritory = item
                val territoryObject: TerritoryResponse.TerritoryData =
                    territoryList.find { it.territory == item }!!
                teritoryId = territoryObject.territory_id
            }
        })
    }


    private fun getDataForPresenter() {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = Token
        query["user_id"] = preference.getString("user_id")
        query["user_type"] = preference.getString("user_type").toLowerCase(Locale.ROOT)
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getPresenterData(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PresenterDataResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(presenterData: PresenterDataResponse) {
                    hideProgress()
                    if (presenterData.status == "success" && presenterData.status_code == 200) {
                        presenterDataList.addAll(presenterData.data)
                        updatePresenterDropDown(presenterData.data as ArrayList<PresenterDataResponse.Data>)
                    }
                }
            })
    }

    private fun updatePresenterDropDown(data: ArrayList<PresenterDataResponse.Data>) {
        tvPresenterLabel.visibility = View.VISIBLE
        spinner_presenter_source.visibility = View.VISIBLE
        viewPresenter.visibility = View.VISIBLE
        presenterStringDataList.clear()
        data.forEach {
            presenterStringDataList.add(it.name)
        }

        presenterSpinner = NumberSpinner(this, presenterStringDataList, "Select Presenter")
        presenterSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                presenterObject = null
                spinner_presenter_source.text = item
                presenterObject = presenterDataList.find { it.name == item }

                when (presenterObject?.user_type) {
                    "dhanuka_doctor" -> {
                        monitorDataList.clear()
                        presenterDataList.forEach {
                            if (it.user_type != "dhanuka_doctor") {
                                monitorDataList.add(it)
                            }
                        }
                        isMonitorEnable = true
                        tvMonitorLabel.visibility = View.VISIBLE
                        spinner_monitor_source.visibility = View.VISIBLE
                        viewMonitor.visibility = View.VISIBLE
                    }
                    "bdo" -> {
                        monitorDataList.clear()
                        presenterDataList.forEach {
                            if (it.user_type != "dhanuka_doctor" && it.user_type != "bdo") {
                                monitorDataList.add(it)
                            }
                        }
                        if (monitorDataList.size > 0) {
                            isMonitorEnable = true
                            tvMonitorLabel.visibility = View.VISIBLE
                            spinner_monitor_source.visibility = View.VISIBLE
                            viewMonitor.visibility = View.VISIBLE
                        }
                    }

                    "sales_area" -> {
                        monitorDataList.clear()
                        presenterDataList.forEach {
                            if (it.user_type != "dhanuka_doctor" && it.user_type != "bdo" && it.user_type != "sales_area") {
                                monitorDataList.add(it)
                            }
                        }
                        if (monitorDataList.size > 0) {
                            isMonitorEnable = true
                            tvMonitorLabel.visibility = View.VISIBLE
                            spinner_monitor_source.visibility = View.VISIBLE
                            viewMonitor.visibility = View.VISIBLE
                        }
                    }

                    "area" -> {
                        monitorDataList.clear()
                        presenterDataList.forEach {
                            if (it.user_type != "dhanuka_doctor" && it.user_type != "bdo" && it.user_type != "sales_area" && it.user_type != "area") {
                                monitorDataList.add(it)
                            }
                        }
                        if (monitorDataList.size > 0) {
                            isMonitorEnable = true
                            tvMonitorLabel.visibility = View.VISIBLE
                            spinner_monitor_source.visibility = View.VISIBLE
                            viewMonitor.visibility = View.VISIBLE
                        }
                    }

                    else -> {
                        isMonitorEnable = false
                        monitorDataList.clear()
                        tvMonitorLabel.visibility = View.GONE
                        spinner_monitor_source.visibility = View.GONE
                        viewMonitor.visibility = View.GONE
                    }
                }
                if (isMonitorEnable) {
                    setMonitorData()
                }
            }
        })
    }

    private fun setMonitorData() {
        monitorStringDataList.clear()
        monitorDataList.forEach {
            monitorStringDataList.add(it.name)
        }
        monitorSpinner =
            NumberSpinner(this@PlanAtpActivity, monitorStringDataList, "Select Monitor")
        monitorSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                monitorObject = null
                spinner_monitor_source.text = item
                monitorObject = monitorDataList.find { it.name == item }
            }
        })
    }

    private fun updateCropBeanList(id: Int) {

        prodCropResponseModel.forEach {
            if (it.productId.equals(id.toString())) {
                var crop = CropBean()
                crop.setcID(it.cropId.toInt())
                crop.item = it.crop
                cropList.add(crop)
            }
        }
        /* cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);*/
        cropSpinner?.updatelist(cropList)
        // cropSpinner.showSpinerDialog()
    }

    override fun onDateClick(dateTime: DateTime?) {
        if (dateTime?.monthOfYear() != null && dateTime.year() != null) {
            if (dateTime.monthOfYear().get() > DateTime().monthOfYear().get() + 1) {
                calendar.reset()
                makeToast("You can only plan for 1 month in advance.")
                Handler().postDelayed({ }, 300)
                return
            }
        }
        if (dateTime?.monthOfYear() != null && dateTime.year() != null) {
            if (dateTime <= DateTime().minusDays(1)) {
                calendar.reset()
                date = DateTime().plusDays(1).toString();
                makeToast("You can not plan for past dates")
                Handler().postDelayed({
                    updateCalenderWithSelectedTime()
                    calendar.setStartDate(DateTime().plusDays(1), ListOFSelectedDated)
                    calendar.setSelectedDate(DateTime().plusDays(1))
                    tvMonth.text = DateTime().plusDays(1).dayOfMonth()
                        .getAsText(Locale.ENGLISH) + " " + DateTime().monthOfYear()
                        .getAsText(Locale.ENGLISH) + ", " + DateTime().year()
                        .getAsText(Locale.ENGLISH)
                }, 300)
                return
            }
        }

        if (dateTime?.monthOfYear() != null && dateTime.year() != null) {
            if (dateTime <= DateTime().minusDays(0)) {
                calendar.reset()
                date = DateTime().plusDays(1).toString();
                makeToast("You can not plan for current date")
                Handler().postDelayed({
                    updateCalenderWithSelectedTime()
                    calendar.setStartDate(DateTime().plusDays(1), ListOFSelectedDated)
                    calendar.setSelectedDate(DateTime().plusDays(1))
                    tvMonth.text = DateTime().plusDays(1).dayOfMonth()
                        .getAsText(Locale.ENGLISH) + " " + DateTime().monthOfYear()
                        .getAsText(Locale.ENGLISH) + ", " + DateTime().year()
                        .getAsText(Locale.ENGLISH)
                    /*date = dateTime.toString()*/
                }, 300)
                return
            }
        }
        if (!(dateTime?.monthOfYear() == null || dateTime.year() == null)) {
            tvMonth.text =
                dateTime.dayOfMonth().getAsText(Locale.ENGLISH) + " " + dateTime.monthOfYear()
                    .getAsText(Locale.ENGLISH) + ", " + dateTime.year().getAsText(Locale.ENGLISH)

            date = dateTime.toString()
        }
        updateRemainingDaysTV()


    }

    override fun setItems(items: DistrictModel) {
        val districtbeanList = items.data
        if (districtbeanList!!.size > 0) {
            DatabaseManager.deleteAllDistrictData(this, state_id)
            districtbeanList.forEach {
                val bean = DistrictDBModel()
                bean.id = it.getdID()!!
                bean.stateId = state_id
                bean.districtName = it.item
                DatabaseManager.saveDistrictData(this, bean)
            }
        }
        val updatedVal = DatabaseManager.getDistrictData(this, state_id)
        districtList.clear()
        districtList.addAll(updatedVal)
        districSpinner?.updatelist(districtList)
    }

    override fun setItems(items: TehsilModel) {
        val tehsilbeanList = items.data
        if (tehsilbeanList!!.size > 0) {
            DatabaseManager.deleteAllTehsilData(this, district_id)
            tehsilbeanList.forEach {
                val model = TehsilDBModel()
                model.id = it.gettID()!!
                model.did = district_id
                model.tehsilName = it.item
                DatabaseManager.saveTehsilData(this, model)
            }
        }
        val tehsilUpdatedVal = DatabaseManager.getTehsilData(this, district_id)
        tehsilList.clear()
        tehsilList.addAll(tehsilUpdatedVal)
        tehsilSpinner?.updatelist(tehsilList)
        hideProgress()
    }

    override fun onInvalidAttempt() {
        hideProgress()
    }

    private fun updateListData() {
        stateModelLis.clear()
        dataSourceList.clear()
        productList.clear()
        cropList.clear()
        val state = DatabaseManager.getStateModel(this)
        stateModelLis.addAll(state)
        languageList.clear()
        languageList = DatabaseManager.getLanguageList(this)
        updateCropList()
        stateSpinner?.updatelist(stateModelLis)
        dataSourceSpinner?.updatelist(dataSourceList)
        productSpinner?.updatelist(productList)
        languageSpinner?.updateList(languageList)
    }

    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()

    private fun updateProductList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        if (prodCropResponseModel != null) {
            prodCropResponseModel.forEach {
                val bean = ProductBean()
                bean.setpID(it.productId.toInt())
                bean.item = it.product
                if (!checkValue(bean.getpID()!!))
                    productList.add(bean)
            }
        }
    }

    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        val prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(this)
        prpdCropListNew?.forEach {
            val bean = CropBean()
            bean.setcID(it.cropId.toInt())
            bean.item = it.crop
            if (!checkValue(bean.getcID()!!))
                cropList.add(bean)

        }
    }

    private fun checkValue(id: Int): Boolean {
        productList.forEach {
            if (id == it.getpID()) {
                return true
            }
        }
        return false
    }

    private fun updateProductBeanList(id: Int, position: Int) {
        val temp = ArrayList<ProductBean>()
        prodCropResponseModel.forEach {
            if (it.cropId.equals(id.toString())) {
                val product = ProductBean()
                product.setpID(it.productId.toInt())
                product.item = it.product
                temp.add(product)
            }
        }

        productList.clear()
        productList.addAll(temp)
        productSpinner?.updatelist(productList)

        productList2.clear()
        productList2.addAll(temp)
        productSpinnerOne?.updatelist(productList2)

        productList3.clear()
        productList3.addAll(temp)
        productSpinnerTwo?.updatelist(productList3)
    }

    private fun initiateBackendcall() {
        observerCrop = MyContentObserver()
        observerDatasources = MyContentObserver()
        observerProduct = MyContentObserver()
        observerState = MyContentObserver()
        observerCrop.setListener(this)
        observerDatasources.setListener(this)
        observerProduct.setListener(this)
        observerState.setListener(this)
        applicationContext.contentResolver
            .registerContentObserver(StateTable.CONTENT_URI, true, observerState)
        applicationContext.contentResolver
            .registerContentObserver(ProductTable.CONTENT_URI, true, observerProduct)
        applicationContext.contentResolver
            .registerContentObserver(CropTable.CONTENT_URI, true, observerCrop)
        applicationContext.contentResolver
            .registerContentObserver(DataSourceTable.CONTENT_URI, true, observerDatasources)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            val manager = DataSyncManager(this, this)
            manager.getPlanRequirment()
            showProgress()
        }
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.spinnerLanguage -> {
                languageSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.spinner_presenter_source -> {
                if (presenterDataList.size > 0) {
                    presenterSpinner?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }

                }
            }
            R.id.spinner_monitor_source -> {
                if (monitorDataList.size > 0) {
                    monitorSpinner?.let {
                        if (!it.isEnable) {
                            it.showSpinerDialog()
                        }
                    }

                }
            }
            R.id.spinner_crop -> {
                cropSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.spinner_Product -> {
                productSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.spinner_Product_one -> {
                productSpinnerOne?.let {
                    if (!it.isEnable && product.isNotEmpty())
                        if (productList2.size >= 1) {
                            it.showSpinerDialog()
                        } else {
                            makeToast("There is only one product.")
                        }
                    else
                        makeToast("Please select the Product One.")
                }

            }
            R.id.spinner_Product_two -> {
                productSpinnerTwo?.let {
                    if (!it.isEnable && product.isNotEmpty() && prod_one.isNotEmpty())
                        if (productList3.size >= 2) {
                            it.showSpinerDialog()
                        } else {
                            makeToast("There is only two product.")
                        }
                    else
                        makeToast("Please select the Product & Product Two")
                }
            }
            R.id.spinner_dd -> {
                val intent = Intent(this@PlanAtpActivity, DDListActivity::class.java)
                startActivityForResult(intent, 100)
            }
            R.id.spinner_data_source -> {
                dataSourceSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.spinner_territory -> {
                selectTerritory?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.spinner_state -> {
                stateSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.spinner_tehsil -> {
                tehsilSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.spinner_district -> {
                districSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }

            }
            R.id.etRetVillage -> {
                villageSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }

            }
            R.id.tvReAddNewActivity -> {
                saveAtp(false)
            }
            R.id.iCross -> {
                finish()
            }
            R.id.submit -> {
                submitATP()
                //saveAtp(true)
            }
        }
    }

    private fun saveAtp(value: Boolean) {
        val ef_str = etRetfarmer.text.toString()
        expected_farmer = if (ef_str.isNullOrEmpty()) {
            0
        } else {
            Integer.parseInt(etRetfarmer.text.toString())
        }
        val pd = etRetpda.text.toString()
        expected_pda = if (pd.isNullOrEmpty()) {
            0
        } else
            Integer.parseInt(etRetpda.text.toString())

        val resp = checkValues()
        if (resp.equals("perfect", ignoreCase = true)) {
            counter++
            val model = PlanAtpDBModel()
            model.product_id = product_id
            model.crop_id = crop_id
            model.territory_id = teritoryId
            model.territory = teritory
            model.presenter_id =
                if (presenterObject != null) presenterObject!!.id.toString() else ""
            model.presenter_type =
                if (presenterObject != null) presenterObject!!.user_type.toString() else ""
            model.monitor_id =
                if (monitorObject != null) monitorObject!!.id.toString() else if (presenterObject != null) presenterObject!!.id.toString() else ""
            model.monitor_type =
                if (monitorObject != null) monitorObject!!.user_type.toString() else if (presenterObject != null) presenterObject!!.user_type.toString() else ""
            model.data_source_id = data_source_id
            model.state_id = state_id
            model.district_id = district_id
            model.tehsil_id = tehsil_id
            model.expected_farmer = expected_farmer
            model.isUploaded = 0
            model.crop_one = prod_one
            model.crop_two = prod_two
            model.village = village
            model.expected_pda = expected_pda
            model.record_id = preference.getString("ecn") + System.currentTimeMillis().toString()
            model.date = date
            model.languageId = language_id
            model.product = product
            model.crop = spinner_crop.text.toString()
            model.state = state
            model.district = district
            model.tehsil = tehsil
            model.dD_ID = ddID
            model.iS_FL = isFl
            DatabaseManager.savePlanAtp(this, model)
            logEventCreateAtp("save locally", Gson().toJson(model), false)
            upDateRcView()
            updateRemainingDaysTV()
            restoreDefault()
            updateCalenderWithSelectedTime()
            calendar.setStartDate(DateTime().plusDays(1), ListOFSelectedDated)
            if (value)
                showdialog()
        } else {
            makeToast(resp)
        }
    }

    private fun logEventCreateAtp(s: String, query: String?, b: Boolean) {
        val logModel = LogModel()
        logModel.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val data = "Create Atp data : $query"
        logModel.apiData = data
        logModel.apiName = s
        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        logModel.userData = "\nUSER DATA : -> $userData"
        AppLogFile.writeLineInFile(Gson().toJson(logModel), preference.getString("ecn"))
    }

    private fun upDateRcView() {
        planAtpList.clear()
        val test = DatabaseManager.getAllPlanATPList(this, 0)
        planAtpList.addAll(test)
        planAtpList.sort()
        adapter.notifyDataSetChanged()
        llNestedScroll.scrollTo(0, llNestedScroll.bottom)
        rvListPlanAtpList.scrollToPosition(planAtpList.size - 1)
    }

    private fun restoreDefault() {
        tvPresenterLabel.visibility = View.GONE
        spinner_presenter_source.visibility = View.GONE
        viewPresenter.visibility = View.GONE
        tvMonitorLabel.visibility = View.GONE
        spinner_monitor_source.visibility = View.GONE
        viewMonitor.visibility = View.GONE
        spinner_presenter_source.text = ""
        spinner_monitor_source.text = ""
        presenterObject = null
        monitorObject = null
        isMonitorEnable = false

        data_source_id = 0
        teritoryId = 0
        teritory = ""
        district_id = 0
        tehsil_id = 0
        expected_farmer = 0
        village = ""
        expected_pda = 0
        spinner_presenter_source.text = "Select Presenter"
        spinner_territory.text = "Select Presenter"
        spinner_monitor_source.text = "Select Monitor"
        spinner_data_source.text = "Select Meeting type"
        spinner_district.text = "Select District"

        spinner_tehsil.text = "Select Tehsil"
        etRetfarmer.text.clear()
        etRetpda.text.clear()
        spinner_dd.text = "Select DD"
        ddID = ""

        village = ""
        etRetVillage.text = "Please select village"
    }

    override fun OnDbValueChange() {
    }

    fun checkValues(): String {

        if (data_source_id == 0) {
            return "Please Select Type of Meeting"
        } else if (language_id == 0) {
            return "Please Select Language"
        } else if (product_id == 0) {
            return "Please Select Product"
        } else if (crop_id == 0) {
            return "Please Select Crop"
        } else if (data_source.equals("village meeting", false) && prod_one.isNullOrEmpty()) {
            return "Please Select Crop Two"
        } else if (data_source.equals("village meeting", false) && prod_two.isNullOrEmpty()) {
            return "Please Select Crop Three"
        } else if (state_id == 0) {
            return "Please Select State"
        } else if (district_id == 0) {
            return "Please Select District"
        } else if (tehsil_id == 0) {
            return "Please Select Tehsil"
        } else if (date.isNullOrEmpty()) {
            return "Please Select a Date"
        } else if (ddID == "") {
            return "Please Select a DD"
        } else if (village == "") {
            return "Please Select a Village"
        } else if (isFl == "0") {
            return "Please Select a assign ATP"
        } else if (spinner_data_source.text.toString().equals("E-Meeting", ignoreCase = true)) {
            return if (presenterObject == null) {
                "Please select presenter"
            } else {
                if (isMonitorEnable) {
                    if (monitorObject == null) {
                        "Please select monitor of E-Meeting."
                    } else {
                        "perfect"
                    }
                } else {
                    "perfect"
                }
            }
        } else return "perfect"
    }

    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_ticket_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
        ticketNumber.visibility = View.GONE
        tic_banner.visibility = View.GONE
        msg.text = "Your Plan is saved"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            finish()
        }
        dialog.show()
    }

    override fun onLongClick(view: View, atpDbModel: PlanAtpDBModel, position: Int) {
        androidx.appcompat.app.AlertDialog.Builder(this)
            .setTitle(getResources().getString(R.string.nav_item_delete))
            .setMessage(getResources().getString(R.string.txt_delete_comment))
            .setPositiveButton("Yes", DialogInterface.OnClickListener { dialog, which ->
                DatabaseManager.deletePlanAtp(this, atpDbModel.id)
                upDateRcView()
            })
            .setNegativeButton("No", DialogInterface.OnClickListener { dialog, which -> })
            .show()
    }

    override fun onDBSyncSuccess() {
        if (state_id != 0) {
            val query = HashMap<String, Any>()
            query.put("apiToken", Token)
            query.put("sId", state_id)
            if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
                mdistrictPresenter.onSubmitClick(query)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
                hideProgress()
            }
        }

    }

    override fun onFailure(msg: String) {
        hideProgress()
    }

    fun filterOther(value: Int) {
        when (value) {
            1 -> {
                var bean: ProductBean? = null
                productList2.clear()
                productList2.addAll(productList)
                productList2.forEach {
                    if (it.item.equals(product)) {
                        bean = it
                    }
                }
                productList2.remove(bean)
                productSpinnerOne?.updatelist(productList2)
                //productList3.addAll(productList2)

            }
            2 -> {
                var bean: ProductBean? = null
                productList3.clear()
                productList3.addAll(productList2)
                productList3.forEach {
                    if (it.item.equals(prod_one)) {
                        bean = it
                    }
                }
                productList3.remove(bean)
                productSpinnerTwo?.updatelist(productList3)
            }
            3 -> {
                if (!product.isNullOrEmpty()) {
                    filterOther(1)
                }
                if (!prod_one.isNullOrEmpty()) {
                    filterOther(2)
                }
            }
        }
    }

    private fun getModelTobeUploaded(): ArrayList<PlanAtpDBModel> {
        val model1 = DatabaseManager.getAllPlanATPList(this, 0)
        val model_ret = ArrayList<PlanAtpDBModel>()
        model_ret.addAll(model1)
        return model_ret
    }

    private fun submitATP() {
        val modelQuery = getModelTobeUploaded()
        if (modelQuery.size != 0) {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                makeRequest(Token, modelQuery)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        } else {
            makeToast("There is no plan ATP")
        }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == 100) {
            ddID = data?.getStringExtra("id") ?: ""
            ddName = data?.getStringExtra("ddName") ?: ""
            spinner_dd.text = ddName
        }
    }

    /*
    * village list acc. to tehsil
    * */
    private fun getVillageList(tehsilID: String) {

        val query = HashMap<String, Any>()
        query.put("apiToken", Token)
        query.put("tehsil_id", tehsilID)


        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).tehshilVillageList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<VillageListTehsil>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable) {
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                    logEventCustom(
                        e.localizedMessage ?: "Custom Message -> error is null",
                        "v2/tehshilVillageList"
                    )
                    logEventCreateAtp("v2/tehshilVillageList Error", Gson().toJson(e), true)
                }

                override fun onNext(villageBean: VillageListTehsil) {
                    villageList.clear()
                    villageList.addAll(villageBean.data)
                    villageSpinner?.updatelist(villageBean.data)
                    if (villageBean.statusCode != 200) {
                        logEventCreateAtp(
                            "v2/tehshilVillageList Error",
                            Gson().toJson(villageBean),
                            true
                        )
                        logEventCustom(
                            villageBean.message,
                            "v2/tehshilVillageList"
                        )
                    }
                }
            })

    }

    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = apiName
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        Utility.logEvent("PlanAtpActivity", "failed_api_$apiName", bundle)
    }

    /*
    * data source list
    * */
    private fun getDataSourceList(token: String) {

        val query = HashMap<String, Any>()


        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).dataSourceList(token)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DataSourceListBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(dataSourceBean: DataSourceListBean) {
                    dataSourceList.addAll(dataSourceBean.data)
                    dataSourceSpinner?.updatelist(dataSourceBean.data)

                }
            }
            )
    }

    /*
    * make request for submit ATP
    * */
    private fun makeRequest(token: String, planAtp: ArrayList<PlanAtpDBModel>) {

        showProgress()
        val test: ArrayList<PAtpApiModel> = ArrayList()
        planAtp.forEach {
            val its = PAtpApiModel()
            its.crop_id = it.crop_id
            its.product_id = it.product_id
            its.plan_date = it.date
            its.data_source_id = it.data_source_id
            its.district_id = it.district_id
            its.state_id = it.state_id
            its.language_id = it.languageId
            its.tehsil_id = it.tehsil_id
            its.expected_farmer = it.expected_farmer
            its.expected_pda = it.expected_pda
            its.record_id = it.record_id
            its.territory_id = it.territory_id
            its.monitor_id = it.monitor_id
            its.presenter_id = it.presenter_id
            its.monitor_type = it.monitor_type
            its.presenter_type = it.presenter_type
            its.village = it.village
            if (it.crop_one == "") {
                its.product_id_2 = 0
            } else {
                its.product_id_2 = it.crop_one.toInt()
                its.crop_id1 = it.crop_id
            }
            if (it.crop_two == "") {
                its.product_id_3 = 0
            } else {
                its.product_id_3 = it.crop_two.toInt()
                its.crop_id2 = it.crop_id
            }

            its.forwhom = it.iS_FL.toInt()
            its.dd_id = it.dD_ID
            test.add(its)
        }
        val model = PlanATPApiModel()
        model.apiToken = token
        model.planATP = test
        logEventCreateAtp("Api called : v3/planAtpSubmit", Gson().toJson(model), false)
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).planAtpSubmit(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<PlanAtpSubmitResponseModel>() {
                override fun onCompleted() { }

                override fun onError(e: Throwable) {
                    logEventCreateAtp("Error Api called : v3/planAtpSubmit", Gson().toJson(e), true)
                    hideProgress()
                    makeToast("Failure")
                    upDateRcView()
                    updateRemainingDaysTV()
                    restoreDefault()
                    updateCalenderWithSelectedTime()
                    calendar.setStartDate(DateTime().plusDays(1), ListOFSelectedDated)
                }

                override fun onNext(homeBean: PlanAtpSubmitResponseModel?) {
                    if (homeBean?.data == null) {
                        hideProgress()
                        logEventCreateAtp(
                            "Error Api called : v3/planAtpSubmit",
                            Gson().toJson(homeBean),
                            true
                        )
                        makeToast(homeBean?.message)
                    } else {
                        logEventCreateAtp(
                            "Plan atp success : v3/planAtpSubmit",
                            Gson().toJson(homeBean),
                            false
                        )
                        DatabaseManager.deleteAllPlanAtp(this@PlanAtpActivity)
                        upDateRcView()
                        updateRemainingDaysTV()
                        restoreDefault()
                        updateCalenderWithSelectedTime()
                        calendar.setStartDate(DateTime().plusDays(1), ListOFSelectedDated)
                        hideProgress()
                        makeToast(homeBean.message)
                    }
                }
            })
    }
}

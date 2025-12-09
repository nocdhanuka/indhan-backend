package com.dhanuka.indhan.acpfarmers

import android.content.Intent
import android.location.Address
import android.location.Geocoder
import android.location.Location
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.LocationActivity
import com.dhanuka.indhan.util.PermissionsUtil
import com.google.android.gms.maps.CameraUpdateFactory
import com.google.android.gms.maps.GoogleMap
import com.google.android.gms.maps.GoogleMap.OnCameraIdleListener
import com.google.android.gms.maps.OnMapReadyCallback
import com.google.android.gms.maps.SupportMapFragment
import com.google.android.gms.maps.model.CameraPosition
import com.google.android.gms.maps.model.LatLng
import com.google.android.gms.maps.model.MarkerOptions
import kotlinx.android.synthetic.main.activity_acp_farmer_map_list.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*


class AcpFarmerMapList : LocationActivity(), OnMapReadyCallback {
    override fun onClick(v: View?) {

    }


    private var farmerList = mutableListOf<ACPFarmerBean.Data.ACPDetail>()

    private var map: GoogleMap? = null
    private lateinit var preference: Preference
    private lateinit var data: ACPFarmerBean
    var mapView: View? = null
    private var isCurrentLocation = false
    private var mLatitude: Double = 0.0
    private var mLongitude: Double = 0.0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_acp_farmer_map_list)
        preference = Preference(this)
        //getACPList()
//        if (intent.hasExtra("data")) {
//            data = intent.getSerializableExtra("data") as ACPFarmerBean
//        }

        val mapFragment = supportFragmentManager.findFragmentById(R.id.map) as SupportMapFragment?
        mapView = mapFragment!!.view
        setMarker(0.0, 0.0)

        mapFragment!!.getMapAsync(this)

        imgSearch.setOnClickListener {
            getACPList(mLatitude, mLongitude)
        }
        search.setOnClickListener {
            getACPList(mLatitude, mLongitude)

        }


    }

    override fun onLocationChanged(location: Location?) {

        if (!isCurrentLocation) {
            if (location != null) {
                isCurrentLocation = true
                setMarker(location?.latitude, location?.longitude)

            }
        }
    }

    override fun onMapReady(p0: GoogleMap?) {
        map = p0

        PermissionsUtil.askPermission(this, PermissionsUtil.LOCATION) { isGranted ->
            if (isGranted) {
                requestLocation()
                map?.isMyLocationEnabled = true

            }
        }


        //setMarker(0.0,0.0)


        map?.setOnMarkerClickListener { marker ->
            val position = marker.snippet
            val i = Intent(this, AcpFarmerDetailsActivity::class.java)
            i.putExtra("data", data.data.aCPDetail[position.toInt()])
            startActivity(i)
            false
        }


    }

    protected fun setCurrentMarker(latitude: Double?, longitude: Double?, position: Int) {
        val latLng1 = LatLng(latitude!!, longitude!!)

        val marker = map?.addMarker(
            MarkerOptions().position(latLng1)
                .title(getCompleteAddress(latitude.toString(), longitude.toString()))
        )
        marker?.hideInfoWindow()
        marker?.tag = ""
        marker?.snippet = position.toString()
        map?.uiSettings?.isZoomControlsEnabled = true
        map?.uiSettings?.isCompassEnabled = true
        map?.uiSettings?.setAllGesturesEnabled(true)
        map?.moveCamera(CameraUpdateFactory.newLatLngZoom(LatLng(latitude, longitude), 12.0f))
    }

    private fun getCompleteAddress(LATITUDE: String, LONGITUDE: String): String {
        var strAdd = ""
        val geocoder = Geocoder(this, Locale.getDefault())
        var addresses: List<Address> = emptyList()

        try {
            addresses = geocoder.getFromLocation(LATITUDE.toDouble(), LONGITUDE.toDouble(), 1)
            if (addresses != null) {
                var returnedAddress: Address = addresses.get(0)
                var strReturnedAddress: StringBuilder = StringBuilder("")

                for (i in 0..returnedAddress.getMaxAddressLineIndex()) {
                    strReturnedAddress.append(returnedAddress.getAddressLine(i)).append("\n");
                }
                strAdd = strReturnedAddress.toString()
            } else {
            }
        } catch (e: Exception) {
        }

        return strAdd

    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        PermissionsUtil.onRequestPermissionsResult(requestCode, permissions, grantResults)
    }


    private fun getACPList(latitude: Double, longitude: Double) {
        showProgress()
        val query = HashMap<String, Any>()
        query["latitude"] = latitude
        query["apiToken"] = preference.getString("token")
        query["longitude"] = longitude


        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getMApFarmerList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ACPFarmerBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(listBean: ACPFarmerBean) {

                    hideProgress()
                    if (listBean.statusCode == 200) {
                        if (listBean.data.aCPDetail.size > 0) {
                            data = listBean
                            map?.clear()
                            farmerList.clear()
                            farmerList.addAll(data.data.aCPDetail)
                            for (item in farmerList.indices) {
                                if (farmerList[item].farmerLat != "null" && farmerList[item].farmerLat != null && farmerList[item].farmerLong != null && farmerList.get(
                                        item
                                    ).farmerLong != "null"
                                ) {
                                    setCurrentMarker(
                                        farmerList[item].farmerLat.toDouble(),
                                        farmerList[item].farmerLong.toDouble(),
                                        item
                                    )
                                }
                            }
                            hideProgress()
                        } else {
                            makeToast("No data found")
                        }
                    } else {

                    }
                }
            })

    }


    //[Set Marker]
    fun setMarker(latitude: Double?, longitude: Double?) {
        map?.getUiSettings()?.setZoomControlsEnabled(false)
        // googleMap.getUiSettings().setMyLocationButtonEnabled(false);
        map?.getUiSettings()?.setCompassEnabled(true)
        map?.getUiSettings()?.setAllGesturesEnabled(true)
        val latLng1 = LatLng(latitude!!, longitude!!)
        val cameraPosition = CameraPosition.Builder().target(latLng1).zoom(13f)
            .bearing(90f) // Sets the orientation of the camera to east.tilt(90f)
            .build()
        map?.animateCamera(CameraUpdateFactory.newCameraPosition(cameraPosition))
        //[for location button below]
//        if (mapView != null && mapView!!.findViewById("1".toInt()) != null) { // Get the button view
//            val locationButton: View = (mapView!!.findViewById("1".toInt()).getParent() as View).findViewById("2".toInt())
//            // and next place it, on bottom right (as Google Maps app)
//            val layoutParams = locationButton.layoutParams as RelativeLayout.LayoutParams
//            // position on right bottom
//            layoutParams.addRule(RelativeLayout.ALIGN_PARENT_TOP, 0)
//            layoutParams.addRule(RelativeLayout.ALIGN_PARENT_BOTTOM, RelativeLayout.TRUE)
//            layoutParams.setMargins(0, 0, 30, 30)
//            //[End]
//        }
        //[Get address when moving map]
        map?.setOnCameraIdleListener {
            if (map?.cameraPosition?.target?.latitude != 0.0 && map?.cameraPosition?.target?.longitude != 0.0) {
                mLatitude = map?.cameraPosition?.target?.latitude!!
                mLongitude = map?.cameraPosition?.target?.longitude!!
                //makeToast("new ")
                //getACPList(map?.cameraPosition?.target?.latitude!!,map?.cameraPosition?.target?.longitude!!)
                //                findAddress(googleMap.getCameraPosition().target.latitude, googleMap.getCameraPosition().target.longitude)
            }
        }


    }


}

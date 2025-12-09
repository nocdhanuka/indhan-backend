package com.dhanuka.indhan.Profile

import android.content.Context
import android.content.Intent
import androidx.viewpager.widget.PagerAdapter
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.RelativeLayout
import android.widget.TextView
import com.dhanuka.indhan.AllAcres.AllAcresActivity
import com.dhanuka.indhan.AllEarnings.AllEarningsActivity
import com.dhanuka.indhan.MyVillage.MyVillageActivity
import com.dhanuka.indhan.R

class ProfileAdapter(context: Context, model: DDProfileBean, var size: Int) : PagerAdapter() {

    var context: Context = context
    var model: DDProfileBean = model

    override fun getItemPosition(`object`: Any): Int {
        return POSITION_NONE
    }

    override fun instantiateItem(container: ViewGroup, position: Int): Any {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_profile, container, false)

        //val binding:ItemReviewBinding=view.rootView as

        val txtEarning = view.findViewById(R.id.txtEarning) as com.dhanuka.indhan.Fonts.BoldTextView
        val recyclerView = view.findViewById(R.id.recyclerView) as RecyclerView
        var btnViewAll =
            view.findViewById(R.id.btnViewAll) as com.dhanuka.indhan.Fonts.RegularButton
        var txtOne = view.findViewById(R.id.txtOne) as TextView
        var txtTwo = view.findViewById(R.id.txtTwo) as TextView
        var txtThree = view.findViewById(R.id.txtThree) as TextView

        var txtMyEarning = view.findViewById<com.dhanuka.indhan.Fonts.Regular>(R.id.txtMyEarning)











        when (position) {
            0 -> {
                txtOne.text = "Earning Date"
                txtTwo.text = "Amount"
                txtMyEarning.text = "My Earnings"


                txtEarning.text = model.data.totalEarning
                var adapter = ProfileEarningAdapter(context, model.data.myEarning)
                recyclerView.layoutManager =
                    LinearLayoutManager(context)
                recyclerView.adapter = adapter
                btnViewAll.setOnClickListener {
                    val intent = Intent(context, AllEarningsActivity::class.java)
                    intent.putExtra("key", model.data)
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
                    context.startActivity(intent)
                }

            }
            1 -> {
                txtOne.text = "Village Name"
                txtTwo.text = "No. of Farmer"
                txtMyEarning.text = "Total Villages"
                txtEarning.text = model.data.totalVillages.toString()
                txtThree.visibility = View.GONE


                btnViewAll.setOnClickListener {
                    val intent = Intent(context, MyVillageActivity::class.java)
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
                    context.startActivity(intent)
                }


                var adapter = ProfileVillageAdapter(context, model.data.myVillages)
                recyclerView.layoutManager =
                    LinearLayoutManager(context)
                recyclerView.adapter = adapter
            }
            else -> {
                txtOne.text = "Village Name"
                txtTwo.text = "Acres"
                txtMyEarning.text = "Total Acres"
                txtThree.visibility = View.GONE


                txtEarning.text = model.data.totalAcres.toString()

                btnViewAll.setOnClickListener {
                    val intent = Intent(context, AllAcresActivity::class.java)
                    intent.putExtra("key", model.data)
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
                    context.startActivity(intent)
                }

                var adapter = ProfileAcresAdapter(context, model.data.myAcres)
                recyclerView.layoutManager =
                    LinearLayoutManager(context)
                recyclerView.adapter = adapter
            }
        }


//        val ratingBar:RatingBar=view.findViewById(R.id.ratingBar)as RatingBar
//        val editText:EditText=view.findViewById(R.id.edtWrite)as EditText
//
//        ratingBar.setOnRatingBarChangeListener { ratingBar, rating, fromUser ->
//           list[position].rating=rating.toInt()
//
//        }
//        editText.addTextChangedListener(object :TextWatcher{
//            override fun afterTextChanged(s: Editable?) {
//                list[position].review=s.toString()
//
//            }
//
//            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {
//            }
//
//            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
//
//            }
//
//        })


        container.addView(view)
        return view

    }

    override fun isViewFromObject(view: View, `object`: Any): Boolean {
        return view == `object`
    }

    override fun getCount(): Int {
        return size
    }

    override fun destroyItem(container: ViewGroup, position: Int, `object`: Any) {
        container.removeView(`object` as RelativeLayout)
    }

    override fun getPageWidth(position: Int): Float {

        return 0.90f

    }


}



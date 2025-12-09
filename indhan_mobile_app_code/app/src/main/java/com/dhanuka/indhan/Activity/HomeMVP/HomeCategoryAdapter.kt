package com.dhanuka.indhan.Activity.HomeMVP

import android.app.Activity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.RelativeLayout
import com.bumptech.glide.Glide
//import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.HomeMVP.HomeCategoryAdapter.MyViewHolder
import com.dhanuka.indhan.Activity.HomeMVP.Model.MenuBean
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.home_category_row.view.*
import java.util.*

/**
 * Created by Shivam on 2/27/2018.
 */

class HomeCategoryAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<MenuBean>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<MyViewHolder>() {

    private val filteredData: List<MenuBean>
    private lateinit var clickListener: HomeCategoryClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                clickListener.onClick(
                    view,
                    filteredData[adapterPosition]
                )
            }
        }
    }

    fun setClickListener(itemClickListener: HomeCategoryClickListener) {
        this.clickListener = itemClickListener
    }


    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.home_category_row, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val menuBean = filteredData[position]
        if (menuBean.tag_icon != null) {
            Glide.with(mActivityContext)
                .load(menuBean.base_url + menuBean.tag_icon)
//                    .centerCrop()
                //.placeholder(R.drawable.)
//                    .crossFade()
                .into(holder.itemView.IvImage)
        } else {
            holder.itemView.IvImage.visibility = View.GONE
            val layoutParams =
                holder.itemView.tvMeCategoryName.layoutParams as RelativeLayout.LayoutParams
            layoutParams.addRule(RelativeLayout.CENTER_IN_PARENT, RelativeLayout.TRUE)
            holder.itemView.tvMeCategoryName.layoutParams = layoutParams
            holder.itemView.tvMeCategoryName.textSize = 25f
        }
        holder.itemView.tvMeCategoryName.text = menuBean.item
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }
}

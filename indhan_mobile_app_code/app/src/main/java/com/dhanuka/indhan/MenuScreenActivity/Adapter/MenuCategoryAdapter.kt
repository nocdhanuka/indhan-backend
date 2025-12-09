package com.dhanuka.indhan.MenuScreenActivity.Adapter

import android.app.Activity
import android.graphics.drawable.Drawable
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.RelativeLayout
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.dhanuka.indhan.MenuScreenActivity.Model.MenuDBModel
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_menu_item.view.*

/**
 * Created by Shivam on 2/27/2018.
 */

class MenuCategoryAdapter(
    homeCategoryRecordResponseModelArrayList: ArrayList<MenuDBModel>,
    private val mActivityContext: Activity
) :
    RecyclerView.Adapter<MenuCategoryAdapter.MyViewHolder>() {


    private val filteredData: List<MenuDBModel>
    private lateinit var clickListener: MenuClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((filteredData != null) && (filteredData.size > adapterPosition) && (adapterPosition != -1)) {
                    clickListener.onClick(view, filteredData[adapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: MenuClickListener) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_menu_item, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val menuBean = filteredData[position]
        if (menuBean.image_url != null) {
            Glide.with(mActivityContext)
                .load(menuBean.image_url)
                .into(holder.itemView.IvImage)
        } else {
            if (menuBean.name.equals("Data Collection", ignoreCase = true)) {
                val res: Drawable =
                    mActivityContext.resources.getDrawable(R.drawable.ic_data_collection)
                holder.itemView.IvImage.setImageDrawable(res)
            } else {
                holder.itemView.IvImage.visibility = View.GONE
                val layoutParams =
                    holder.itemView.tvMeCategoryName.layoutParams as RelativeLayout.LayoutParams
                layoutParams.addRule(RelativeLayout.CENTER_IN_PARENT, RelativeLayout.TRUE)
                holder.itemView.tvMeCategoryName.layoutParams = layoutParams
                holder.itemView.tvMeCategoryName.textSize = 25f
            }
        }
        when {
            menuBean.name.lowercase().equals("atp", ignoreCase = true) -> {
                holder.itemView.tvMeCategoryName.text = "View ATP"
            }
            menuBean.name.equals("testimonials", ignoreCase = true) -> {
                holder.itemView.tvMeCategoryName.text = "Master Testimonials"
            }
            menuBean.name.lowercase().equals("my syllabus", ignoreCase = true) -> {
                holder.itemView.tvMeCategoryName.text = "Create ATP"
            }
            else -> {
                holder.itemView.tvMeCategoryName.text = menuBean.name
            }
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface MenuClickListener {
        fun onClick(view: View, menuBean: MenuDBModel)
    }

}

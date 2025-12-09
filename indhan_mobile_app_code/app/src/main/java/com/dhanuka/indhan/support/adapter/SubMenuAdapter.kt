package com.dhanuka.indhan.support.adapter


import android.app.Activity
import android.content.Context
import android.graphics.drawable.Drawable
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.RelativeLayout
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Constants.Constants.LIVEURL
import com.dhanuka.indhan.R
import com.dhanuka.indhan.support.model.SupportSubMenuBean
import kotlinx.android.synthetic.main.layout_menu_item.view.*

class SubMenuAdapter(
    val context: Context,
    val subMenuList: List<SupportSubMenuBean>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<SubMenuAdapter.ViewHolder>() {

    private val subMenus: List<SupportSubMenuBean> = subMenuList
    private lateinit var clickListener: SubMenuClickListener

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((subMenuList != null) && (subMenuList.size > adapterPosition) && (adapterPosition != -1)) {
                    clickListener.onClick(view, subMenuList[adapterPosition])
                }
            }
        }
    }

    fun setClickListener(itemClickListener: SubMenuClickListener) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): SubMenuAdapter.ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_menu_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: SubMenuAdapter.ViewHolder, position: Int) {
        val catName = holder.itemView.tvMeCategoryName
        if (subMenuList[position].iconImage != null) {
            Glide.with(mActivityContext)
                .load(LIVEURL + subMenuList[position].iconImage)
                .into(holder.itemView.IvImage)
        } else {
            when {
                subMenuList[position].campaignTypeName.equals(
                    "Channels",
                    ignoreCase = true
                ) -> {
                    val res: Drawable =
                        mActivityContext.resources.getDrawable(R.drawable.ic_distributor)
                    holder.itemView.IvImage.setImageDrawable(res)
                }
                subMenuList[position].campaignTypeName.equals(
                    "Molecules",
                    ignoreCase = true
                ) -> {
                    val res: Drawable =
                        mActivityContext.resources.getDrawable(R.drawable.ic_chemical_icon)
                    holder.itemView.IvImage.setImageDrawable(res)
                }
                else -> {
                    val layoutParams = catName.layoutParams as RelativeLayout.LayoutParams
                    layoutParams.addRule(RelativeLayout.CENTER_IN_PARENT, RelativeLayout.TRUE)
                    catName.layoutParams = layoutParams
                    catName.textSize = 25f
                }
            }
        }
        catName.text = subMenuList[position].campaignTypeName
    }

    override fun getItemCount(): Int {
        return subMenuList.size
    }

    interface SubMenuClickListener {
        fun onClick(view: View, subMenu: SupportSubMenuBean)
    }


}
package com.dhanuka.indhan.DemoRequestEvaluated.adapters


import android.app.Activity
import android.content.Context
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.RelativeLayout
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoSubMenu
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_menu_item.view.*

class DemoSubMenuAdapter(
    val context: Context,
    val subMenuList: List<DemoSubMenu>,
    private val mActivityContext: Activity
) : RecyclerView.Adapter<DemoSubMenuAdapter.ViewHolder>() {

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

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): DemoSubMenuAdapter.ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_menu_item, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: DemoSubMenuAdapter.ViewHolder, position: Int) {
        val catName = holder.itemView.tvMeCategoryName
        if (!TextUtils.isEmpty(subMenuList[position].tag_icon)) {
            Glide.with(mActivityContext)
                .load("${subMenuList[position].base_url}/${subMenuList[position].tag_icon}")
                .into(holder.itemView.IvImage)
        } else {
            val layoutParams = catName.layoutParams as RelativeLayout.LayoutParams
            layoutParams.addRule(RelativeLayout.CENTER_IN_PARENT, RelativeLayout.TRUE)
            catName.layoutParams = layoutParams
            catName.textSize = 25f
        }
        catName.text = subMenuList[position].item
    }

    override fun getItemCount(): Int {
        return subMenuList.size
    }

    interface SubMenuClickListener {
        fun onClick(view: View, subMenu: DemoSubMenu)
    }


}